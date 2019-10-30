<?php
	include_once '../config/app_config.php';
	include_once '../config/Database.php';
	include_once '../models/Collections.php';
	include_once '../authorize.php';// successfully sign in, $user_id, $username and $home_collection_id are set.
	include '../collection_view.php';// for function "genAddNewColl($collection)" and "genOneEle($collection,$subEle)"

	//connect to DB
	$database = new Database();
	$connection = $database->connect();
	
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

			$author_id = $user_id;
			$newSubCollection_id = $result = $collection->addNewSubCollection($collection_title, $author_id);
			
		if(isset($_POST['ajax'])){
		// request by ajax
			if($result){
				$newSubCollection = new Collection($connection);
				$newSubCollection->id = $newSubCollection_id;
				$newSubCollection->read();
				header("Content-type:text/html");
				genOneEle($collection, $newSubCollection);
				genAddNewColl($newSubCollection);
				echo '<ul class="subcollections hide"></ul>';
				echo '<ul class="lists hide"></ul>';
			}else{
				http_response_code(500);
			}
		}else{
			if($result){		
				$_SESSION['message'] = 'Collection is created';
			}else{
				$_SESSION['message'] = 'Collection is not created';
			}
			// back to current collection
			header("Location:../collection_template.php");
		}
	}
	
?>
