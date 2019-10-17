<?php
	include_once '../config/app_config.php';
	include_once '../config/Database.php';
	include_once '../models/ItemX.php';
	include '../authorize.php';// successfully sign in, $user_id, $username and $home_collection_id are set.
	//connect to DB
	$database = new Database();
	$connection = $database->connect();
	var_dump($connection);echo "<br/>";

	//get data from query string $_GET
$item_types_reg = "/^".ITEM_TYPE."|".CHECK_TYPE."|".TASK_TYPE."$/";// supported types
		if(isset($_GET['item_id']) && preg_match('/^[0-9]+$/',$_GET['id']) && isset($_GET['type']) && preg_match($item_types_reg,$_GET['item_type'])){
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
		
