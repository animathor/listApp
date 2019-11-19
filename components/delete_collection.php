<?php
/*
** Delete collection.
** // 1) Check authorization 
** // 2) Ajax?
** // 3) Get id
** // 4) Connect to Database and prepare model
** // 5) Delete the collection
** // 6) Respond
*/
	include_once '../config/app_config.php';
	include_once '../config/Database.php';
	include_once '../models/Collections.php';
	
	// 1) Check authorization 
	include '../authorize.php';// successfully sign in, $user_id, $username and $home_collection_id are set.
	// 2) Ajax?
	$ajax = isset($_POST['ajax']);
	
	try{
		// 3) get data from query string $_GET
		if(isset($_GET['id']) && preg_match('/^[0-9]+$/',$_GET['id'])){
			$collection_id= $_GET['id'];
		}else{
			throw new Exception();
		}
		// 4) connect to DB
		$database = new Database();
		$connection = $database->connect();

		$collection = new Collection($connection);
		$collection->id = $collection_id;
	}catch(dbConnectException $dbe){
		if($ajax){
				http_response_code(500);
				exit;
		}else{
			$_SESSION['meassage'] = $e->getMessage();
			header("Location:../collection.php");
		}
	}catch(Exception $e){
		if($ajax){
				header("Content-type:text/html");
				http_response_code(400);
				echo $e->getMessage();
				exit;
		}else{
			$_SESSION['meassage'] = $e->getMessage();
			header("Location:../collection.php");
		}
	}

	// 5) delete collection
	$result = $collection->delete();

	// 6) Respond
	if($ajax){
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
		header("Location:../collection.php");
	}
	
?>
