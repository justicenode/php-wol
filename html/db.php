<?php
class Database extends mysqli{
	private $servername = "localhost";
	private $username = "root";
	private $password = "";
	private $database = "php-wol";
	private $mysqli;
	
	function __construct() {
		// Create connection
		parent::__construct($this->servername, $this->username, $this->password);

		// Check connection
		if ($this->connect_error) {
			die("Connection failed: " . $this->connect_error);
		}
		
		//check if database exists
		$result = $this->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '{$this->database}'");
		if(mysqli_num_rows($result) <= 0) $this->create();
		else $this->query("USE `{$this->database}`");
	}
	
	/**
	 * creates a new database
	 */
	function create() {
		$query = "
CREATE DATABASE `{$this->database}`;
USE `{$this->database}`;
CREATE TABLE server (
  id varchar(45) NOT NULL PRIMARY KEY,
  name varchar(30) NOT NULL,
  ip varchar(16) NOT NULL,
  mac varchar(20) UNIQUE,
  broadcast varchar(16)
);

CREATE TABLE user (
  id int AUTO_INCREMENT NOT NULL PRIMARY KEY,
  username varchar(30) NOT NULL UNIQUE,
  password varchar(128) NOT NULL,
  level int NOT NULL
);

INSERT INTO user VALUES (1, 'admin', '1879303f48fc69acc84e6b24608b2c7b932c18f90546186507b4513b44ce4ad2bfb360c223142239c6828201f4d05a3a6357429d2bd9cd44ed06b87b03e4a96a', 3);
		";
		$this->multi_query($query);
	}
}
?>