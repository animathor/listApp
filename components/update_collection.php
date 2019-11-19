<?php
/*
** update collection.
** // 1) Check authorization 
** // 2) Ajax?
** // 3) Get id
** // 4) Connect to Database and prepare model
** // 5) Validate title(<255)
** // 6) Update collection
** // 7) Respond
*/
	include_once '../config/app_config.php';
	include_once '../config/Database.php';
	include_once '../models/ItemX.php';
	// 1) Check authorization 
	include '../authorize.php';// successfully sign in, $user_id, $username and $home_collection_id are set.
	// 2) Ajax?
	$ajax = isset($_POST['ajax']);
	
	try{	

		// 3) Get id
		if(isset($_GET['id']) && preg_match('/^[0-9]+$/',$_GET['id'])){
		$id= $_GET['id'];

		}else{
			throw new Exception();
		}
		
		// 4) Connect to Database and prepare model
		$database = new Database();
		$connection = $database->connect();
		$collection = new Collection($connection);
		
		// 5) Validate title(<255)
		// get title
		$title = "blank";
			if($_POST['title']){
				if(strlen($_POST['title'])>255){
					throw new Exception('Title should be at most 255 charactors');
				}
				$title = empty($_POST['title'])? 'blank' : htmlspecialchars($_POST['title']);
			}
	}catch(dbConnectException $dbe){
		if($ajax){
				header("Content-type:application/json");
				echo json_encode(["success"=>false,
														"message"=>$e->getMessage()]);
				http_response_code(500);
				exit;
		}else{
			$_SESSION['meassage'] = $e->getMessage();
			header("Location:../collection.php");
		}
	}catch(Exception $e){
		if($ajax){
				http_response_code(400);
				header("Content-type:application/json");
				echo json_encode(["success"=>false,
														"message"=>$e->getMessage()]);
				exit;
		}else{
			$_SESSION['meassage'] = $e->getMessage();
			header("Location:../collection.php");
		}
	}
		
	$collection->id = $id;
	$collection->title = $title;
	
		// 6) Update collection
		$result = $collection->update();
		
		// 7) Respond
		if($ajax){
		// request by ajax
			if($result){
				header("Content-type:application/json");
				echo json_encode(["success"=>true,
														"title"=>$title]);
			}else{
				header("Content-type:application/json");
				echo json_encode(["success"=>false,
														"message"=>"collection is not updated"]);
			}
		}else{
			if($result){
				$_SESSION['message'] = 'collection is updated';
			}else{
				$_SESSION['message'] = 'collection is not updated';
			}
			header("Location:../collection.php");
		}
