<?php

	include_once '../config/app_config.php';
	include_once '../config/Database.php';
	include_once '../models/ItemX.php';
	include '../authorize.php';// successfully sign in, $user_id, $username and $home_collection_id are set.
	//connect to DB
	$database = new Database();
	$connection = $database->connect();

	//get data from query string $_GET
		if(isset($_GET['item_id']) && preg_match('/^[0-9]+$/',$_GET['item_id']) && isset($_GET['item_type']) && preg_match(SUPPORT_TYPES_REG,$_GET['item_type'])){
		$item_id= $_GET['item_id'];
		$item_type = $_GET['item_type'];
		$id_type_arr = ['id'=>$item_id, 'type'=>$item_type];
		if(isset($_POST['item'])){
			$order = $_POST['item'];
  
			//use itemX
			$item = new ItemX($connection,$id_type_arr);
			$item->setData('order',$order);
			$result = $item->updateOrder();
				if($result){
					header("Content-type:application/json");
					echo json_encode(["success"=>true]);
				}else{
					header("Content-type:application/json");
					echo json_encode(["success"=>false]);
				}
		}
	}
?>
