<?php
	include_once '../config/app_config.php';
	include_once '../config/Database.php';
	include_once '../models/Collections.php';
	include_once '../models/Items_obj.php';
	include_once '../authorize.php';// successfully sign in, $user_id, $username and $home_collection_id are set.

	//connect to DB
	$database = new Database();
	$connection = $database->connect();

	// get collection_id from get
		if(isset($_GET['id']) && preg_match('/^[0-9]+$/',$_GET['id']) && isset($_REQUEST['list_type']) && preg_match(SUPPORT_TYPES_REG,$_REQUEST['list_type'])){
		$collection_id = $_GET['id'];
		var_dump($_GET);
		// get type from $_POST
		$list_type = $_REQUEST['list_type'];
			// create list
		$collection = new Collection($connection);
		$collection->id = $collection_id;
		
		echo '<pre>';
		echo var_dump($collection);
		echo "</pre>";
		
		$author_id = $user_id;
		if($id = $collection->addNewList('New list',$list_type,$author_id)){
		var_dump($id);
			header("Location:../list_template.php?id=".$id."&type=".$list_type);
		}else{
			header("Location:../collection_template.php");	
		}

	}
	
?>
