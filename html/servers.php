<?php
	include_once 'auth.php';
	include_once 'db.php';
	
	$auth = new Auth();
	$db = new Database();
	
	header('Content-Type: application/json');
	if(!isset($_POST['action']) || $_POST['action'] == "get"){
		$st = $db->prepare("SELECT * FROM server");
		$st->execute();
		
		echo json_encode($st->fetchAll());
	}
	else if ($_POST['action'] == "add" && isset($_POST['id'],$_POST['name'],$_POST['mac'],$_POST['ip'],$_POST['broadcast'])){
		if($auth->hasLevel(2)) {
			$st = $db->prepare("INSERT INTO server VALUES (?, ?, ?, ?, ?)");
			$st->execute([$_POST['id'], $_POST['name'], $_POST['ip'], $_POST['mac'], $_POST['broadcast']]);
			if($st->errorCode() == 0) echo '{"status":200, "reponse":"Success"}';
			else {
				http_response_code(400);
				echo json_encode([
					'status' => 400,
					"error" => $st->errorInfo()
				]);
			}
		}
		else {
			http_response_code(401);
			echo '{"status":401, "reponse":"Unauthorized"}';
		}
	}
	else if($_POST['action'] == "remove" && isset($_POST['id'])){
		if($auth->hasLevel(2)) {
			$st = $db->prepare("DELETE FROM server WHERE id = ?");
			$st->execute([$_POST['id']]);
			echo '{"status":200, "reponse":"success"}';
		}
		else {
			http_response_code(401);
			echo '{"status":401, "reponse":"Unauthorized"}';
		}
	}
	else if($_POST['action'] == "modify" && isset($_POST['id'],$_POST['name'],$_POST['mac'],$_POST['ip'],$_POST['broadcast'])){
		if($auth->hasLevel(2)) {
			$st = $db->prepare('UPDATE server SET name=?, ip=?, mac=?, broadcast=? WHERE id=?');
			$st->execute([$_POST['name'], $_POST['ip'], $_POST['mac'], $_POST['broadcast'], $_POST['id']]);
			echo '{"status":200, "reponse":"Success"}';
		}
		else {
			http_response_code(401);
			echo '{"status":401, "reponse":"Unauthorized"}';
		}
	}
