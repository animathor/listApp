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
		if(isset($_GET['item_id']) && preg_match('/^[0-9]+$/',$_GET['item_id']) && isset($_GET['item_type']) && preg_match($item_types_reg,$_GET['item_type'])){
		$item_id= $_GET['item_id'];
		$item_type = $_GET['item_type'];
		$id_type_arr = ['id'=>$item_id, 'type'=>$item_type];
		//delete item 
		$item = new ItemX($connection,$id_type_arr);
		
		$result = $item->delete();
		if(isset($_POST['ajax'])){
		// request by ajax
			if($result){
				header("Content-type:application/json");
				echo json_encode(["success"=>true]);
			}else{
				echo json_encode(["success"=>false,
													"message"=>'item'.$item_id.' is not deleted']);
			}
		}else{
		// request by normal 
			if($result){
				$_SESSION['message'] = 'item'.$item_id.' is deleted';
			}else{
				$_SESSION['message'] = 'item'.$item_id.' is not deleted';
			}
			header("Location:../list_template.php");
		}
	}
?>
