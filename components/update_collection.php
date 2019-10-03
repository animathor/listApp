<?php
	include_once '../config/Database.php';
	include_once '../models/ItemX.php';

	//connect to DB
	$database = new Database();
	$connection = $database->connect();
	var_dump($connection);echo "<br/>";

	//get data from query string $_GET
	if($_GET){
		$id= $_GET['id'];
		$collection = new Collection($connection);
		$collection->id = $id;
		
		// get title
		$title = "";
		if($_POST){
			$title = $_POST['collection_title'];
		}
		
		$collection->title = $title;
		
		session_start();
		if($collection->update()){
			$_SESSION['message'] = 'collection is updated';
		}else{
			$_SESSION['message'] = 'collection is not updated';
		}
	}
	header("Location:../collection_template.php");
		
