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
		$hash = hash('sha512', $user['level'].'g$6|@#'.$user['id'].$password);
		
		if ($user['password'] == $hash) {
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
	
	function getUsername(){
		return $_SESSION['user']['username'];
	}
}
