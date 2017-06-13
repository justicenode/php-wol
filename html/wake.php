<?php
	include_once 'auth.php';
	include_once 'db.php';
	
	$auth = new Auth();
	
	if(!$auth->hasLevel()) {
		http_response_code(401);
	}
	else if(isset($_POST['mac'], $_POST['broadcast'])) {
		$broadcast =  $_POST['broadcast'];
		$mac_array = split(':', $_POST['mac']);

		$hwaddr = '';

		foreach($mac_array AS $octet) {
			$hwaddr .= chr(hexdec($octet));
		}

		// Create Magic Packet

		$packet = '';
		for ($i = 1; $i <= 6; $i++) {
			$packet .= chr(255);
		}

		for ($i = 1; $i <= 16; $i++) {
			$packet .= $hwaddr;
		}

		$sock = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
		if($sock) {
			$options = socket_set_option($sock, 1, 6, true);

			if ($options >=0) {    
				$e = socket_sendto($sock, $packet, strlen($packet), 0, $broadcast, 7);
				socket_close($sock);
			}    
		}
	}
	else {
		http_response_code(400);
	}
	
	$response = array(
		"status" => http_response_code()
	);
	header('Content-Type: application/json');
	echo json_encode($response);
?>