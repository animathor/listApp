<?php
	include_once '../config/Database.php';
	include_once '../models/Collections.php';

	//connect to DB
	$database = new Database();
	$connection = $database->connect();
	var_dump($connection);
	

	//get data from query string $_GET
	if(isset($_GET['id']) && isset($_GET['list_id'])){
		$collection_id= $_GET['id'];
		$list_id = $_GET['list_id'];
		//delete the list under the collection
		$collection = new Collection($connection);
		$collection->id = $collection_id;
		echo '<pre>';
		echo var_dump($collection);
		echo "</pre>";
		session_start();
		if($collection->deleteList($list_id)){
			$_SESSION['message'] = 'List'.$list_id.' was deleted';
		}else{
			$_SESSION['message'] = 'List'.$list_id.' is not deleted';
		}
		// back to current collection
		if(isset($_SESSION['current_collection'])){
			$id = $_SESSION['current_collection'];
			header("Location:../collection_template.php?id=$id");
		}
		header("Location:../collection_template.php");
	}
?>
