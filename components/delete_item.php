<?php
	include_once '../config/Database.php';
	include_once '../models/ItemX.php';

	//connect to DB
	$database = new Database();
	$connection = $database->connect();
	var_dump($connection);
	

	//get data from query string $_GET
	if($_GET){
		$item_id= $_GET['item_id'];
		$item_type = $_GET['item_type'];
		$id_type_arr = ['id'=>$item_id, 'type'=>$item_type];
		//delete item 
		$item = new ItemX($connection,$id_type_arr);
		echo '<pre>';
		echo var_dump($item);
		echo "</pre>";
		session_start();
		if($item->delete()){
			$_SESSION['message'] = 'item'.$item_id.' is deleted';
		}else{
			$_SESSION['message'] = 'item'.$item_id.' is not deleted';
		}
		header("Location:list_template.php");
	}
?>
