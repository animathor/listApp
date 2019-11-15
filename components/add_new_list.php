<?php
/*
** Add new list(item) to collection.
** // 1) Check authorization 
** // 2) Get id 
** // 3) Connect to Database and prepare collection model
** // 4) Read collection and add new list
** // 5) Respond
*/
	include_once '../config/app_config.php';
	include_once '../config/Database.php';
	include_once '../models/Collections.php';
	include_once '../models/Items_obj.php';
	
	// 1) Check authorization 
	include_once '../authorize.php';// successfully sign in, $user_id, $username and $home_collection_id are set.


	try{
		// 2) get collection_id from get
		if(isset($_GET['id']) && preg_match('/^[0-9]+$/',$_GET['id']) && isset($_REQUEST['list_type']) && preg_match(SUPPORT_TYPES_REG,$_REQUEST['list_type'])){
			$collection_id = $_GET['id'];
			// get type from $_POST
			$list_type = $_REQUEST['list_type'];
		}else{
			throw new Exception();
		}
		// 3) connect to DB
		$database = new Database();
		$connection = $database->connect();
		
		// create list
		$collection = new Collection($connection);
	
	}catch(Exception $e){
			$_SESSION['meassage'] = $e->getMessage();
			header("Location:../collection_template.php");
	}
	
	// 4) Read collection and add new list

	$collection->id = $collection_id;
	$author_id = $user_id;
	
	// 5) Respond
	if($id = $collection->addNewList('New list',$list_type,$author_id)){
		header("Location:../list_template.php?id=".$id."&type=".$list_type);
	}else{
		header("Location:../collection_template.php");
	}
?>
