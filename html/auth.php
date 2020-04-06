<?php
include_once 'db.php';

class Auth {
	/**
	 * Default constructor which checks for http authentication
	 */
	function __construct() {
		if(!isset($_SESSION)) session_start();
		
		if(!empty($_SERVER['PHP_AUTH_USER']) && !empty($_SERVER['PHP_AUTH_PW'])) {
			$this->login($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']);
		}
	}

    /**
     * check if user has enough rights to do actions
     * Levels:
     * 0 - Not authenticated
     * 1 - Basic user (View)
     * 2 - Manage Servers (Add, Remove, Edit)
     * 3 - Admin (Everything)
     * @param int $min minimum level required to to that action
     * @return bool
     */
	function hasLevel($min = 1) {
		if(empty($_SESSION['user'])) return $min <= 0;
		return $_SESSION['user']['level'] >= $min;
	}
	
	/**
	 * @return int the access level of the user
	 */
	function getLevel(){
		if(empty($_SESSION['user'])) return 0;
		return $_SESSION['user']['level'];
	}
	
	/**
	 * performs login and returns success
	 * 
	 * @param string $username the username of the user
	 * @param string $password the cleartext password of the user
	 * @return bool if the login was sucessful
	 */
	function login($username, $password){
		$db = new Database();
		$st = $db->prepare("SELECT * FROM user WHERE username = ?");
		$st->execute([$username]);
		if ($st->rowCount() == 0) return false;
		
		$user = $st->fetch();

		if ($user['password'] == hash('sha512', $user['salt'].$password, true)) {
			$_SESSION['user'] = $user;
			return true;
		}
		
		return false;
	}
	
	/**
	 * performs logout for the user (if someone is logged in)
	 */
	function logout() {
		unset($_SESSION['user']);
	}

	/**
	 * @return string username of current user
	 */
	function getUsername(){
		return $_SESSION['user']['username'];
	}

	/**
	 * @return int id of current user
	 */
	function getUserId(){
		return $_SESSION['user']['id'];
	}

	/**
	 * updates the password of the current user
	 * @param $newpassword string new password
	 * @return bool success
	 */
	public function updatePassword($newpassword) {
		if (!$this->hasLevel()) return false;
		$db = new Database();
		$st = $db->prepare('UPDATE `user` SET password=?, salt=? WHERE id=?');
		list($password, $salt) = $this->hashPassword($newpassword);
		$st->execute([$password, $salt, $this->getUserId()]);
		return true;
	}

	private function hashPassword($password) {
		$salt = hash('sha512', rand(), true);
		$hash = hash('sha512', $salt . $password, true);
		return [$hash, $salt];
	}
}
