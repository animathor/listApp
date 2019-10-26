<?php
	include_once '../config/app_config.php';
	include_once '../config/Database.php';
	include_once '../models/ItemX.php';
	include '../authorize.php';// successfully sign in, $user_id, $username and $home_collection_id are set.
	//connect to DB
	$database = new Database();
	$connection = $database->connect();

	//get data from query string $_GET
$item_types_reg = "/^".ITEM_TYPE."|".CHECK_TYPE."|".TASK_TYPE."$/";// supported types
		if(isset($_GET['id']) && preg_match('/^[0-9]+$/',$_GET['id'])){
		$id= $_GET['id'];
		$collection = new Collection($connection);
		$collection->id = $id;
		// get title
		$title = "blank";
		if($_POST){
			$title = empty($_POST['title'])? 'blank' : htmlspecialchars($_POST['title']);
		}
		
		$collection->title = $title;
		

		$result = $collection->update();
		if(isset($_POST['ajax'])){
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
			header("Location:../collection_template.php");
	}
	
	}
		
