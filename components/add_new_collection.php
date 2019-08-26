<?php
	include_once '../config/Database.php';
	include_once '../models/Collections.php';



	//connect to DB
	$database = new Database();
	$connection = $database->connect();
	var_dump($connection);
	
	// get parent collection id
	if(isset($_GET['id'])){
		// prepare model
		$collection = new Collection();
		$collection->id = $_GET['id'];
		// prepaare title
			$collection_title = 'new collection';
			//get data from $_POST
			if(isset($_POST]))){
				print_r($_POST);echo "<br/>";
				$collection_title = $_POST['collection_title'];
			}
		$collection->title = $collection_title;
		// create collection
			session_start();
			if($collection->create()){
				$_SESSION['message'] = 'List is created';
			}else{
				$_SESSION['message'] = 'List is not created';
			}
	}
	// back to current collection
	header("Location:collection_template.php");
	
?>
