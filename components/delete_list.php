<?php
/*
** delete list(item) from collection.
** // 1) Check authorization 
** // 2) Get list id and collection id
** // 3) Connect to Database and prepare collection model
** // 4) Delete the list
** // 5) Respond
*/
	include_once '../config/app_config.php';
	include_once '../config/Database.php';
	include_once '../models/Collections.php';
	include '../authorize.php';// successfully sign in, $user_id, $username and $home_collection_id are set.
	
	$ajax = isset($_POST['ajax']);

	try{
	// 2) Get list id and collection id
		if(isset($_GET['id']) && preg_match('/^[0-9]+$/',$_GET['id']) && isset($_GET['list_id']) && preg_match('/^[0-9]+$/',$_GET['id'])){
		$collection_id= $_GET['id'];
		$list_id = $_GET['list_id'];
		}else{
			throw new Exception();
		}
		 // 3) connect to DB
		$database = new Database();
		$connection = $database->connect();
	
		//delete the list under the collection
		$collection = new Collection($connection);
		$collection->id = $collection_id;
		
	}catch(dbConnectException $dbe){
		if($ajax){
				header("Content-type:application/json");
				echo json_encode(["success"=>false,
														"message"=>$e->getMessage()]);
				http_response_code(500);
				exit;
		}else{
			$_SESSION['meassage'] = $e->getMessage();
			header("Location:../collection_template.php");
		}
	}catch(Exception $e){
		if($ajax){
				http_response_code(400);
				header("Content-type:application/json");
				echo json_encode(["success"=>false,
														"message"=>$e->getMessage()]);
				exit;
		}else{
			$_SESSION['meassage'] = $e->getMessage();
			header("Location:../collection_template.php");
		}
	}
	// 4) Delete the list
	$result = $collection->deleteList($list_id);
	// 5) Respond
	if($ajax){
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
?>
