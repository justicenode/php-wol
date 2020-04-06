<?php
class Database extends PDO {
	private string $servername = "localhost";
	private string $username = "root";
	private string $password = "";
    private string $database = "php-wol";
	
	function __construct() {
	    // Get configuration
        if (file_exists('./config.php')) {
            [
                "servername" => $this->servername,
                "username" => $this->username,
                "password" => $this->password,
                "database" => $this->database,
            ] = include('./config.php');
        }

		// Create connection
		try {
			parent::__construct("mysql:{$this->servername}", $this->username, $this->password);
		} catch (PDOException $e) {
			die('Connection failed: ' . $e->getMessage());
		}
		
		//check if database exists
		$st = $this->prepare("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '{$this->database}'");
        $st->execute();
		if($st->rowCount() <= 0) $this->create();
		else $this->query("USE `{$this->database}`");
	}
	
	/**
	 * creates a new database
	 */
	function create() {
		$this->query("CREATE DATABASE `{$this->database}`");
		$this->query("USE `{$this->database}`");
		$this->query(<<<EOL
CREATE TABLE server (
  id varchar(45) NOT NULL PRIMARY KEY,
  name varchar(30) NOT NULL,
  ip varchar(16) NOT NULL,
  mac varchar(20) UNIQUE,
  broadcast varchar(16)
);
EOL);
		$this->query(<<<EOL
CREATE TABLE user (
  id int AUTO_INCREMENT NOT NULL PRIMARY KEY,
  username varchar(30) NOT NULL UNIQUE,
  password varchar(128) NOT NULL,
  level int NOT NULL
);
EOL);
		$this->query(
"INSERT INTO user VALUES (1, 'admin', '1879303f48fc69acc84e6b24608b2c7b932c18f90546186507b4513b44ce4ad2bfb360c223142239c6828201f4d05a3a6357429d2bd9cd44ed06b87b03e4a96a', 3);"
		);
	}
}
