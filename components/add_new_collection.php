<?php
/*
** Add new subcolletion to collection.
** // 1) Check authorization 
** // 2) Ajax?
** // 3) Get id
** // 4) Connect to Database and prepare model
** // 5) Validate title(<255)
** // 6) Read collection and add new subcollection
** // 7) Respond
*/
	include_once '../config/app_config.php';
	include_once '../config/Database.php';
	include_once '../models/Collections.php';
	include '../collection_display.php';// for function "genAddNewColl($collection)" and "genOneEle($collection,$subEle)"

	// 1) Check authorization 
	include_once '../authorize.php';// successfully sign in, $user_id, $username and $home_collection_id are set.
	// 2) Ajax?
	$ajax = isset($_POST['ajax']);

	try{
		// 2) Get parent collection id
		if(isset($_GET['id']) && preg_match('/^[0-9]+$/',$_GET['id'])){
			// prepare model
			
		}else{
			throw new Exception();
		}
		
		// 3) Connect to DB and prepare model
		$database = new Database();
		$connection = $database->connect();
		$collection = new Collection($connection);
		$collection->id = $_GET['id'];
		// 4) Validate title(<255)
		// prepare title
		$collection_title = 'new collection';
		//get data from $_POST
		if(isset($_POST['collection_title']) && strlen($_POST['collection_title']) > 0){
			if(strlen($_POST['collection_title']) > 255){
				throw new Exception('Title should be at most 255 charactors');
			}
			$collection_title = $_POST['collection_title'];
		}
		
	}catch(dbConnectException $dbe){
		if($ajax){
				http_response_code(500);
				exit;
		}else{
			$_SESSION['meassage'] = $e->getMessage();
			header("Location:../collection.php");
		}
	}catch(Exception $e){
		if($ajax){
				header("Content-type:text/html");
				http_response_code(400);
				echo $e->getMessage();
				exit;
		}else{
			$_SESSION['meassage'] = $e->getMessage();
			header("Location:../collection.php");
		}
	}

	// 4) Read collection and add new subcollection
	
	$author_id = $user_id;// $user_id is set in authorize.php
	$newSubCollection_id = $result = $collection->addNewSubCollection($collection_title, $author_id);
	
	// 5) Respond	
	if($ajax){
	// request by ajax
		if($result){
			$newSubCollection = new Collection($connection);
			$newSubCollection->id = $newSubCollection_id;
			$newSubCollection->read();
			header("Content-type:text/html");
			echo '<li data-id="'.$newSubCollection->id.'">';
			genOneEle($collection, $newSubCollection);
			genAddNewColl($newSubCollection);
			echo '<ul class="subcollections hide data-id="'.$newSubCollection->id.'"></ul>';
			echo '<ul class="lists hide data-id="'.$newSubCollection->id.'"></ul>';
			echo "</li>";
		}else{
			header("Content-type:text/html");
			http_response_code(500);
			echo "Fail to add new collection: ".strlen($collection_title);
		}
	}else{
		if($result){		
			$_SESSION['message'] = 'Collection is created';
		}else{
			$_SESSION['message'] = 'Collection is not created';
		}
		// back to current collection
		header("Location:../collection.php");
	}
?>
