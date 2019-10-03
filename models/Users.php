<?php
require_once("Collections.php");

	class User{

		// Database
		protected $connection;
  
		// Properties
		public $id;
		public $username;
		public $email;
		public $password;
		public $home_collection_id;
		//public $group;
		private const USERS_TABLE = "users";

		// Methods

		public function __construct($pdoObj){
			$this->connection = $pdoObj;
		}
		public 	function create(){
			// insert record into database
			$query = "INSERT INTO ".self::USERS_TABLE."(username, email, password)".
								"VALUES (:username, :email, :password);";
			$stm = $this->connection->prepare($query);

			$stm->bindParam(':username',$this->username);
			$stm->bindParam(':email',$this->email);
			$password_hash = password_hash($this->password, PASSWORD_DEFAULT);
			$stm->bindParam(':password', $password_hash);// hash the pwh by using default php alg
			if(!$stm->execute()){
				// execution failed
				foreach($stmt->errorInfo() as $line)
					echo $line."</br>";
				return false;
			}
			// get the user id
			$new_user_id = $this->connection->lastInsertId();
			$this->id = $new_user_id;// write the id into obj

			// create home collection for user
			$new_home_collection = new Collection($this->connection);
			$new_home_collection->title = "My collections";
			$new_home_collection->author_id = $new_user_id;

			if(!$new_home_collection->create()){
				echo "fail to create home collection for user {$new_user_id}";
				return false;
			}
			// get the new_home_collection id
			$new_home_collection_id = $this->connection->lastInsertId();
			$this->home_collection_id = $new_home_collection_id;

			// write the collection id back to the new user's record
			$query = "UPDATE ".self::USERS_TABLE.
								" SET home_collection_id = :home_collection_id ".
								"	WHERE id = :id";
			$stm = $this->connection->prepare($query);
			$stm->bindParam(':home_collection_id', $new_home_collection_id);
			$stm->bindParam(':id', $new_user_id);
			
			if(!$stm->execute()){
				foreach($stmt->errorInfo() as $line)
					echo $line."</br>";
				return false;
			}
			return true;
		}//End create user

		public function read(){
			$query = "SELECT username, email, password, home_collection_id".
								"	FROM ".self::USERS_TABLE." WHERE id = :id";
			$stm = $this->connection->prepare($query);
			$stm->bindParam(':id',$this->id);

			if($stm->execute()){
				$row = $stm->fetch(PDO::FETCH_ASSOC);
				$this->username = $row['username'];
				$this->email = $row['email'];
				$this->password = $row['password'];
				$this->home_collection_id = $row['home_collection_id'];

				return true;
			}
			return false;

		}//End read user

		public function signin($username){
			$query = "SELECT id, username, email, password, home_collection_id".
								"	FROM ".self::USERS_TABLE." WHERE username = :username";
			$stm = $this->connection->prepare($query);
			$stm->bindParam(':username',$username);

			if($stm->execute()){
				$row = $stm->fetch(PDO::FETCH_ASSOC);
				$this->id = $row['id'];
				$this->username = $row['username'];
				$this->email = $row['email'];
				$this->password = $row['password'];
				$this->home_collection_id = $row['home_collection_id'];

				return true;
			}
			return false;
			
		}// end sign in

		public function update(){
			}//End update user
  
		public function delete(){
			}//End delete user
 
 		// Static methods

		static function isRegistered($username,$pdoObj){
			// DB connection
			$connection = $pdoObj;

			$query = "SELECT id".
								"	FROM ".self::USERS_TABLE." WHERE username = :username";
			$stm = $connection->prepare($query);
			$stm->bindParam(':username',$username);

			if($stm->execute()){
				$row = $stm->fetch(PDO::FETCH_ASSOC);
				return $row['id'];
			}
			return false;
		}

		static function search($fieldName, $matchString){
			$usersList = [];// id => username

			// connect to db
			$connection = new Database();

			$query = 'SELECT id, username'.
								' FROM '.self::USERS_TABLE.
								' WHERE :fieldName'.
								' Like :matchString';


			return $usersList;
		}
}
?>
