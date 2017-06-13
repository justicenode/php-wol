<?php
	include_once 'auth.php';
	$auth = new Auth();
	
	if($auth->hasLevel(1)){
		$status = null;
		if(isset($_POST['ip'])){
			$pingresult = exec("ping -c 3 {$_POST['ip']}", $outcome, $status);
			if (0 == $status) {
				$status = "alive";
			} else {
				$status = "dead";
			}
		}
		else{
			http_response_code(400);
		}
	}
	else http_response_code(401);
	
	$response = array(
		"status" => http_response_code(),
		"response" => $status
	);
	header('Content-Type: application/json');
	echo json_encode($response);
?>