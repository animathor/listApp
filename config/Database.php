<?php
	// Database Setting
	class Database{
		private $host = 'localhost';
		private $username = 'root';
		private $password = '1001';
		private $db_name = 'listApp';
		private $connection;
		
		// Connect to Database
		public function connect(){
			$this->connection = null;

			try{
				$dsn = 'mysql:host='.$this->host.';dbname='.$this->db_name;
				$this->connection = new PDO($dsn, $this->username, $this->password);
				$this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			}catch(PDOException $e){
				echo 'Connection Error: '.$e->getMessage();
			}

			return $this->connection;
		}

	}
?>
