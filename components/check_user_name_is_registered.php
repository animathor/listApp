<?php
/*
** check that if the username was registered.
** // 1) Get username from $_POST
** // 2) Connect to Database 
** // 3) Call to User::isRegistered
** // 4) return the result
*/

require '../config/Database.php';
require '../models/Users.php';

	if(isset($_POST['username'])){

		$username = $_POST['username'];
		
		try{
			// 2) Connect to Database 
			$database = new Database();
			$connection = $database->connect();
			
			// 3) Call to User::isRegistered
			$isRegistered = User::isRegistered($username,$connection);
			
			// 4) return the result
			header("Content-type:application/json");			
			echo json_encode(["success"=>true, "isRegistered"=>$isRegistered]);

		}catch(Exception $e){
			http_response_code(500);
		}
	}else{
		header("Content-type:application/json");			
			echo json_encode(["success"=>false, "message"=>"Sorry, something go wrong..."]);
	}
	
?>
