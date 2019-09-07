<?php
	include_once '../config/Database.php';
	include_once '../models/Collections.php';
	include_once '../models/Items_obj.php';


	//connect to DB
	$database = new Database();
	$connection = $database->connect();
	var_dump($connection);
	var_dump($_GET);
	// get collection_id from get
	if(isset($_GET['id']) && $_POST['list_type']){
		$collection_id = $_GET['id'];
		var_dump($_GET);
		// get type from $_POST
		$list_type = $_POST['list_type'];
			// create list
		$collection = new Collection($connection);
		$collection->id = $collection_id;
		
		echo '<pre>';
		echo var_dump($collection);
		echo "</pre>";
		session_start();
		if($id = $collection->addNewList('New list',$list_type)){
		var_dump($id);
			header("Location:list_template.php?id=".$id."&type=".$list_type);
		}else{
			header("Location:collection_template.php");	
		}

	}
	
?>
