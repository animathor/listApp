<?php
	include_once '../config/Database.php';
	include_once '../models/Lists.php';

	//connect to DB
	$database = new Database();
	$connection = $database->connect();
	var_dump($connection);
	//get list id and type
	if(isset($_GET['list_id']) && $_GET['list_type'])){
		$list_id = $_GET['list_id'];
		$list_type = $_GET['list_type'];
		$id_n_type = ['id'=>$list_id, 'type'=>$list_type];
	}
	//get data from $_POST
	if(isset($_POST['item_title']) && !empty($_POST['item_title'])){
		$item_title = $_POST['item_title'];
	// create list
		$currentList = new ItemX($connection, $id_n_type);
		if($currentList->addNewSubitem($item_title)){
			echo '<pre>';
			echo var_dump($currentList);
			echo "</pre>";
		}else{
			$_SESSION['message'] = 'List is not created';
		}
	}
	header("Location:list_template.php");
?>
