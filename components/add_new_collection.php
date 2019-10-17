<?php
	include_once '../config/Database.php';
	include_once '../models/Collections.php';
	include_once '../authorize.php';// successfully sign in, $user_id, $username and $home_collection_id are set.


	//connect to DB
	$database = new Database();
	$connection = $database->connect();
	var_dump($connection);
	
	// get parent collection id
	if(isset($_GET['id']) && preg_match('/^[0-9]+$/',$_GET['id'])){
		// prepare model
		$collection = new Collection($connection);
		$collection->id = $_GET['id'];
// $user_id is set in authorize.php
		// prepaare title
			$collection_title = 'new collection';
			//get data from $_POST
			if(isset($_POST['collection_title'])){
				$collection_title = $_POST['collection_title'];
			}
		// create collection
			session_start();
			$author_id = $user_id;			
			if($collection->addNewSubCollection($collection_title, $author_id)){
				$_SESSION['message'] = 'Collection is created';
				echo '<pre>';
				var_dump($collection);
				echo '</pre>';
			}else{
				$_SESSION['message'] = 'Collection is not created';
			}
	}
	// back to current collection
	header("Location:../collection_template.php");
	
?>
