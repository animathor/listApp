<?php
	include_once '../config/app_config.php';
	include_once '../config/Database.php';
	include_once '../models/Collections.php';
	include '../authorize.php';// successfully sign in, $user_id, $username and $home_collection_id are set.
	//connect to DB
	$database = new Database();
	$connection = $database->connect();
	
	//get data from query string $_GET
		if(isset($_GET['id']) && preg_match('/^[0-9]+$/',$_GET['id']) && isset($_GET['list_id']) && preg_match('/^[0-9]+$/',$_GET['id'])){
		$collection_id= $_GET['id'];
		$list_id = $_GET['list_id'];
		//delete the list under the collection
		$collection = new Collection($connection);
		$collection->id = $collection_id;

		$result = $collection->deleteList($list_id);
		
		if(isset($_POST['ajax'])){
		// request by ajax
			if($result){
				header("Content-type:application/json");
				echo json_encode(["success"=>true]);
			}else{
				echo json_encode(["success"=>false,
													"message"=>'List is not deleted']);
			}
		}else{
			if($result){
				$_SESSION['message'] = 'List'.$list_id.' was deleted';
			}else{
				$_SESSION['message'] = 'List'.$list_id.' is not deleted';
			}
			// back to current collection
			header("Location:../collection_template.php");
		}
	}
?>
