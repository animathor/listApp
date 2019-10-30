<?php
	include_once '../config/app_config.php';
	include_once '../config/Database.php';
	include_once '../models/ItemX.php';
	include_once '../authorize.php';// successfully sign in, $user_id, $username and $home_collection_id are set.
	include '../item_view.php';// for function "genEditForm($item,$isItem)" and "genSubitemTo($item, $level)"
	
	//connect to DB
	$database = new Database();
	$connection = $database->connect();
	//get list id and type
		if(isset($_GET['list_id']) && preg_match('/^[0-9]+$/',$_GET['list_id']) && isset($_GET['list_type']) && preg_match(SUPPORT_TYPES_REG,$_GET['list_type'])){
		$list_id = $_GET['list_id'];
		$list_type = $_GET['list_type'];
		$id_n_type = ['id'=>$list_id, 'type'=>$list_type];
	}
	//get title from $_POST
	if(isset($_POST['item_title']) && !empty($_POST['item_title'])){
	
		$item_title = $_POST['item_title'];
	// author
		$author_id = $user_id;// $user_id store in authorize.php

	// create list
		$currentList = new ItemX($connection, $id_n_type);
		
		$newSubitem_id=$result=$currentList->addNewSubitem($author_id, $item_title);
		
		
		if(isset($_POST['ajax'])){
		// request by ajax
			if($result){
				$newSubItemX = new ItemX($connection, ["id"=>$newSubitem_id]);
				$newSubItemX->read();
				$newSubItem = $newSubItemX->container;
				header("Content-type:text/html");
				genEditForm($newSubItem, true);
				genAddNew($newSubItem);
				echo '<ul class="suitems hide" data-id="'.$newSubItem->id.'" data-type="'.$newSubItem->type.'"></ul>';
			}else{
				http_response_code(500);
			}
		}else{
			if($result){
				$_SESSION['message'] = 'item is created';
			}else{
				$_SESSION['message'] = 'Sorry, item is not created. Something wrong with the server... Please try again later';
			}
			header("Location:../list_template.php");
		}
	}
?>
