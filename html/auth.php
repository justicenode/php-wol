<?php
include_once 'db.php';

class Auth {
	/**
	 * Default constructor which checks for http authentication
	 */
	function __construct() {
		if(!isset($_SESSION)) session_start();
		
		if(!empty($_SERVER['PHP_AUTH_USER']) && !empty($_SERVER['PHP_AUTH_PW'])) {
			login($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']);
		}
	}
	
	/**
	 * check if user has enough rights to do actions
	 * Levels:
	 * 0 - Not authenticated
	 * 1 - Basic user (View)
	 * 2 - Manage Servers (Add, Remove, Edit)
	 * 3 - Admin (Everything)
	 * @param $min minimum level required to to that action
	 */
	function hasLevel($min = 1) {
		if(empty($_SESSION['user'])) return $min <= 0;
		return $_SESSION['user']['level'] >= $min;
	}
	
	/**
	 * @return the access level of the user
	 */
	function getLevel(){
		if(empty($_SESSION['user'])) return 0;
		return $_SESSION['user']['level'];
	}
	
	/**
	 * performs login and returns success
	 * 
	 * @param $username the username of the user
	 * @param $password the cleartext password of the user
	 * @return bool if the login was sucessful
	 */
	function login($username, $password){
		$db = new Database();
		$rs = $db->query("SELECT * FROM user WHERE username = '" . mysqli_real_escape_string($db, $username) . "'")->fetch_assoc();
		if(empty($rs)) return false;
		
		$user = $rs;
		$hash = hash('sha512', $user['level'].'g$6|@#'.$user['id'].$password);
		
		if($user['password'] == $hash) {
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







	/**
	 * stolen from https://stackoverflow.com/questions/2040240/php-function-to-generate-v4-uuid
	 * but it may not even be used right now
	 */
	function uuid() {
		return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
			// 32 bits for "time_low"
			mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),

			// 16 bits for "time_mid"
			mt_rand( 0, 0xffff ),

			// 16 bits for "time_hi_and_version",
			// four most significant bits holds version number 4
			mt_rand( 0, 0x0fff ) | 0x4000,

			// 16 bits, 8 bits for "clk_seq_hi_res",
			// 8 bits for "clk_seq_low",
			// two most significant bits holds zero and one for variant DCE1.1
			mt_rand( 0, 0x3fff ) | 0x8000,

			// 48 bits for "node"
			mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
    );
}
?>
