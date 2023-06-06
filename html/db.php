<?php
class Database extends PDO {
	private $servername = "localhost";
	private $username = "root";
	private $password = "";
	private $database = "php-wol";
	
	function __construct() {
		// Get configuration
		if (file_exists('./config.php')) {
			$config = include('./config.php');
			$this->servername = $config['servername'];
			$this->username = $config['username'];
			$this->password = $config['password'];
			$this->database = $config['database'];
		}

		// Create connection
		try {
			parent::__construct("mysql:{$this->servername}", $this->username, $this->password);
		} catch (PDOException $e) {
			die('Connection failed: ' . $e->getMessage());
		}
		// eventually create schema
		$this->create();
	}
	
	/**
	 * creates schema if it does not exists
	 */
	function create() {
		//check if database exists
		$st = $this->prepare("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '{$this->database}'");
        $st->execute();
		if($st->rowCount() <= 0) {
			$this->query("CREATE DATABASE `{$this->database}`");
		}
		$this->query("USE `{$this->database}`");
		
		$st = $this->prepare("SELECT 1 FROM server");
        $st->execute();
		if($st->rowCount() <= 0) {
			$q = <<<EOL
CREATE TABLE server (
  id varchar(45) NOT NULL PRIMARY KEY,
  name varchar(30) NOT NULL,
  ip varchar(16) NOT NULL,
  mac varchar(20) UNIQUE,
  broadcast varchar(16)
);
EOL;
			$this->query($q);
		}
		
		$st = $this->prepare("SELECT 1 FROM user");
        $st->execute();
		if($st->rowCount() <= 0) {
			$q = <<<EOL
CREATE TABLE user (
  id int AUTO_INCREMENT NOT NULL PRIMARY KEY,
  username varchar(30) NOT NULL UNIQUE,
  password binary(64) NOT NULL,
  salt binary(64) NOT NULL,
  level int NOT NULL
);
EOL;
			$this->query($q);
		}
		
		// create admin
		$st = $this->prepare("SELECT admin FROM user");
        $st->execute();
		if($st->rowCount() <= 0) {
			$this->query("INSERT INTO `user` (`id`, `username`, `password`, `salt`, `level`) VALUES (1, 'admin', 0xa28d2e6472c306c6c57138f5f2c6ff25498e5d3ccd9a63a1bc591f4b5fe1dedd4b4044bcf1cc0b61767f6d7ce8198d94eb432d03fa082261ab4741734f07e9b2, 0xb2c17f0341921fe13306e0d6b4c952d90ad12ce176e962f5d7f28254c51e4345accaafc5d1f003fe346ab8a1bf52330317d1007237ca8f78abd9420d4b5524a3, 3)");
		}
		
		
	}
}
