<?php
	include_once '../config/Database.php';
	include_once '../models/Collections.php';

	//connect to DB
	$database = new Database();
	$connection = $database->connect();
	var_dump($connection);
	

	//get data from query string $_GET
	if(isset($_GET['id'])){
		$collection_id= $_GET['collection_id'];
		//delete collection 
		$collection = new Collections($connection);
		echo '<pre>';
		echo var_dump($collection);
		echo "</pre>";
		session_start();
		if($collection->delete()){
			$_SESSION['message'] = 'collection'.$collection_id.' was deleted';
		}else{
			$_SESSION['message'] = 'collection'.$collection_id.' is not deleted';
		}
		// back to current collection
		if(isset($_SESSION['current_collection']) && is_int($_SESSION['current_collection'])){
			$id = $_SESSION['current_collection'];
			header("Location:collection_template.php?id=$id");
		}
		header("Location:collection_template.php");
	}
?>
