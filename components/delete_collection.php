<?php
	include_once '../config/Database.php';
	include_once '../models/Collections.php';
	include '../authorize.php';// successfully sign in, $user_id, $username and $home_collection_id are set.
	
	//connect to DB
	$database = new Database();
	$connection = $database->connect();
	

	//get data from query string $_GET
	if(isset($_GET['id']) && preg_match('/^[0-9]+$/',$_GET['id'])){
		$collection_id= $_GET['id'];
		//delete collection
		$collection = new Collection($connection);
		$collection->id = $collection_id;

		$result = $collection->delete();

		if(isset($_POST['ajax'])){
		// request by ajax
			if($result){
				header("Content-type:application/json");
				echo json_encode(["success"=>true]);
			}else{
				echo json_encode(["success"=>false,
													"message"=>'collection is not deleted']);
			}
		}else{
		// request by normal 
			if($result){
				$_SESSION['message'] = 'collection'.$collection_id.' was deleted';
			}else{
				$_SESSION['message'] = 'collection'.$collection_id.' is not deleted';
			}
			header("Location:../collection_template.php");
		}
	}
?>
