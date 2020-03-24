<?php
	include_once 'auth.php';
	
	$auth = new Auth();
	$toReturn = array(
		"status" => 200,
		"response" => ""
	);
	
	if(!isset($_POST['a']) || $_POST['a'] == 'status') {
		if($auth->getLevel() == 0)
			$toReturn["response"] = null;
		else
			$toReturn["response"] = array("level" => $auth->getLevel(), "username" => $auth->getUsername());
	}
	else if($_POST['a'] == 'login' && isset($_POST['username'], $_POST['password'])) {
		$toReturn["response"] = $auth->login($_POST['username'], $_POST['password']);
	}
	else if($_POST['a'] == 'logout') {
		$auth->logout();
		$toReturn["response"] = true;
	}
	
	header('Content-Type: application/json');
	echo json_encode($toReturn);
