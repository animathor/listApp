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
			try{
				$stm = $this->connection->prepare($query);
      
				$stm->bindParam(':username',$this->username);
				$stm->bindParam(':email',$this->email);
				$password_hash = password_hash($this->password, PASSWORD_DEFAULT);
				$stm->bindParam(':password', $password_hash);// hash the pwh by using default php alg
				$stm->execute();
      
				// get the user id
				$new_user_id = $this->connection->lastInsertId();
				$this->id = $new_user_id;// write the id into obj
      
				// create home collection for user
				$new_home_collection = new Collection($this->connection);
				$new_home_collection->title = "My collections";
				$new_home_collection->author_id = $new_user_id;
      
				if(!$new_home_collection->create()){
					throw new Exception("fail to create home collection for user {$new_user_id}");
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
				
				$stm->execute();
				return true;
			}catch(Exception $e){
				return false;
			}
		}//End create user

		public function read(){
			$query = "SELECT username, email, password, home_collection_id".
								"	FROM ".self::USERS_TABLE." WHERE id = :id";
			try{
				$stm = $this->connection->prepare($query);
				$stm->bindParam(':id',$this->id);

				$stm->execute();

				$row = $stm->fetch(PDO::FETCH_ASSOC);
				$this->username = $row['username'];
				$this->email = $row['email'];
				$this->password = $row['password'];
				$this->home_collection_id = $row['home_collection_id'];

				return true;
			}catch(Exception $e){
				return false;
			}

		}//End read user

		public function signin($username){
			try{
				$query = "SELECT id, username, email, password, home_collection_id".
									"	FROM ".self::USERS_TABLE." WHERE username = :username";
				$stm = $this->connection->prepare($query);
				$stm->bindParam(':username',$username);
      
				$stm->execute();
      
				$row = $stm->fetch(PDO::FETCH_ASSOC);
				$this->id = $row['id'];
				$this->username = $row['username'];
				$this->email = $row['email'];
				$this->password = $row['password'];
				$this->home_collection_id = $row['home_collection_id'];

				return true;
			}catch(Exception $e){
				return false;
			}
		}// end sign in

		public function update(){
			}//End update user
  
		public function delete(){
			}//End delete user
 
 		// Static methods

		static function isRegistered($username,$pdoObj){
			try{
				// DB connection
				$connection = $pdoObj;

				$query = "SELECT id".
									"	FROM ".self::USERS_TABLE." WHERE username = :username";
				$stm = $connection->prepare($query);
				$stm->bindParam(':username',$username);

				$stm->execute();
				$row = $stm->fetch(PDO::FETCH_ASSOC);
				if($row['id']){
					return true;
				}else{
					return false;
				}
			}catch(PDOException $pdoe){
				throw new Exception("Failed to check the registeration of user ".$username);
			}
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
