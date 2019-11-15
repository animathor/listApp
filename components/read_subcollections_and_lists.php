<?php
/*
** Read subitems and Display it.
** // 1) Check authorization 
** // 2) Get id and type 
** //			id: check int, type: check item type
** // 3) Connect to Database 
** //		  successfully?
** // 4) Read collection ($collection)
** //		  successfully?
** // 5) Display: subcollections and lists: generate the tree to a finite level
*/
	include_once '../config/app_config.php';
	include_once '../config/Database.php';
	include_once '../models/Collections.php';
	include_once '../models/Items_obj.php';
	include '../collection_view.php';// function: genSubCollTo

	// 1) Session start and check the authorization
	include '../authorize.php';// successfully sign in, $user_id, $username and $home_collection_id are set.
	
	// 2) Get id and type
	//: --$_GET (id, type) --back to previous list --back to collection
	if(isset($_GET['id']) && preg_match('/^[0-9]+$/',$_GET['id'])){
			$collection_id = $_GET['id'];
	}else{
				http_response_code(500);
				exit;
	}

	// Success ?
	try{

		// 3) Connect to Database
		$database = new Database();
		$connection = $database->connect();

		// 4) Read collection
		$collection = new Collection($connection);// Create a model for reading the data
		$collection->id = $collection_id;
		if(!$collection->read()){
			// since the current collection can't be deleted, and it always start from home collection.
			// Current collection always exist.
			throw new Exception("Fail to read the collection");
		}
			
	}catch(Exception $e){
		http_response_code(500);
		exit;
	}

	// 5) Display: subitems: generate the tree to a finite level
	header("Content-type:text/html");
	genSubCollTo($collection, 1);
?>
