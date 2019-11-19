<?php
/*
** Move lsit from one collection to another.
** // 1) Check authorization 
** // 2) Get list id 
** // 3) Get draggedListId, formerParentId
** // 4) Connect to Database and prepare model
** // 5) Move list
** // 6) Respond
*/
	include_once '../config/app_config.php';
	include_once '../config/Database.php';
	include_once '../models/Collections.php';
	
	// 1) Check authorization 
	include '../authorize.php';// successfully sign in, $user_id, $username and $home_collection_id are set.
	
	try{
		// 2) Get list id 
		if(isset($_GET['collection_id']) && preg_match('/^[0-9]+$/',$_GET['collection_id'])){
		$collection_id= $_GET['collection_id'];
		}else{
			throw new Exception();
		}
		
		// 3) Get draggedListId, formerParentId
			$draggedListId = filter_input(INPUT_POST,'draggedListId', FILTER_VALIDATE_INT);
			$formerParentId = filter_input(INPUT_POST,'formerParentId', FILTER_VALIDATE_INT);
		if(!$draggedListId || !$formerParentId){
			throw new Exception("Data is not sent.");
		}
		
  	// 4) Connect to Database and prepare model
		$database = new Database();
		$connection = $database->connect();
		$collection = new Collection($connection);
		
		// 5) Move list
		$collection->id = $collection_id;
		$result = $collection->moveList($formerParentId, $draggedListId);
			
	}catch(dbConnectException $dbe){
		header("Content-type:application/json");
		echo json_encode(["success"=>false,
												"message"=>$e->getMessage()]);
		http_response_code(500);
		exit;
	}catch(Exception $e){
		http_response_code(400);
		header("Content-type:application/json");
		echo json_encode(["success"=>false,
		"line"=>$e->getLine(),"code"=>$e->getCode(),
												"message"=>$e->getMessage()]);
		exit;
	}
	
 	// 6) Respond
	if($result){
		header("Content-type:application/json");
		echo json_encode(["success"=>true]);
	}else{
		header("Content-type:application/json");
		echo json_encode(["success"=>false]);
	}
?>
