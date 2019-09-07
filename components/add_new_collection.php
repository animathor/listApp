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
		$collection = new Collection($connection);
		$collection->id = $_GET['id'];
		// prepaare title
			$collection_title = 'new collection';
			//get data from $_POST
			if(isset($_POST['collection_title'])){
				print_r($_POST);echo "<br/>";
				$collection_title = $_POST['collection_title'];
			}
		// create collection
			session_start();
			if($collection->addNewSubCollection($collection_title)){
				$_SESSION['message'] = 'Collection is created';
				echo '<pre>';
				var_dump($collection);
				echo '</pre>';
			}else{
				$_SESSION['message'] = 'Collection is not created';
			}
	}
	// back to current collection
	header("Location:collection_template.php");
	
?>
