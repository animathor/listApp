<?php
	// Database Setting
	class Database{
		private const DB_HOST = 'localhost';
		private const DB_USERNAME = 'root';
		private const DB_PASSWORD = '1001';
		private const DB_NAME = 'listApp';
		private $connection;
		
		// Connect to Database
		public function connect(){
			$this->connection = null;

			try{
				$dsn = 'mysql:host='.self::DB_HOST.';dbname='.self::DB_NAME;
				$this->connection = new PDO($dsn, self::DB_USERNAME, self::DB_PASSWORD);
				$this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			}catch(PDOException $e){
				throw new dbConnectException('Fail to connect to datasbase.');
			}
			return $this->connection;
		}

	}
	
	// database exception
	class dbConnectException extends Exception{
		function __toString(){
			return "Exception".$this->getCode().":".$this->getmessage()."<br />".
							"in ".$this->getFile()."on line".$this->getLine()."<br />";
		}
	}
?>
