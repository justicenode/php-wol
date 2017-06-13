<?php
	include_once 'auth.php';
	include_once 'db.php';
	
	$auth = new Auth();
	$db = new Database();
	
	header('Content-Type: application/json');
	if(!isset($_POST['action']) || $_POST['action'] == "get"){
		$query = "SELECT * FROM server";
		$rs = $db->query($query);
		$servers = array();
		while ($row = $rs->fetch_object()) {
			$servers[] = $row;
		}
		$rs->close();
		
		echo json_encode($servers);
	}
	else if ($_POST['action'] == "add" && isset($_POST['id'],$_POST['name'],$_POST['mac'],$_POST['ip'],$_POST['broadcast'])){
		if($auth->hasLevel(2)) {
			foreach($_POST as $k=>$v) $_POST[$k] = $db->escape_string($v);
			$query = "INSERT INTO server VALUES ('{$_POST['id']}','{$_POST['name']}','{$_POST['ip']}','{$_POST['mac']}','{$_POST['broadcast']}')";
			$result = $db->query($query);
			if($result) echo '{"status":200, "reponse":"Success"}';
			else {
				http_response_code(400);
				echo '{"status":400, "error":"' . $db->error . '"}';
			}
		}
		else {
			http_response_code(401);
			echo '{"status":401, "reponse":"Unauthorized"}';
		}
	}
	else if($_POST['action'] == "remove" && isset($_POST['id'])){
		if($auth->hasLevel(2)) {
			$_POST['id'] = $db->escape_string($_POST['id']);
			$query = "DELETE FROM server WHERE id = '{$_POST['id']}'";
			$db->query($query);
			echo '{"status":200, "reponse":"success"}';
		}
		else {
			http_response_code(401);
			echo '{"status":401, "reponse":"Unauthorized"}';
		}
	}
	else if($_POST['action'] == "modify" && isset($_POST['id'],$_POST['name'],$_POST['mac'],$_POST['ip'],$_POST['broadcast'])){
		if($auth->hasLevel(2)) {
			//TODO: add support for custom broadcast address
			foreach($_POST as $k=>$v) $_POST[$k] = $db->escape_string($v);
			$query = "UPDATE server SET name='{$_POST['name']}',ip='{$_POST['ip']}',mac='{$_POST['mac']}',broadcast='{$_POST['broadcast']}' WHERE id = '{$_POST['id']}'";
			$db->query($query);
			echo '{"status":200, "reponse":"Success"}';
		}
		else {
			http_response_code(401);
			echo '{"status":401, "reponse":"Unauthorized"}';
		}
	}
?>