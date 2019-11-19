<?php
/*
** Read subitems and Display it.
** // 1) Check authorization 
** // 2) Get id and type 
** //			id: check int, type: check item type
** // 3) Connect to Database 
** //		  successfully?
** // 4) Read subitems ($theItem)
** //		  successfully?
** // 5) Display: subitems: generate the tree to a finite level 
*/
	include_once '../config/app_config.php';
	include_once '../config/Database.php';
	include_once '../models/Items_obj.php';
	include '../item_display.php';// for function "genOneItem($item,$isItem)" and "genSubitemTo($item, $level)"

	// 1) Session start and check the authorization
	include '../authorize.php';// successfully sign in, $user_id, $username and $home_collection_id are set.
	
	// 2) Get id and type
	//: --$_GET (id, type) --back to previous list --back to collection
	if(isset($_GET['id']) && preg_match('/^[0-9]+$/',$_GET['id']) && isset($_GET['type']) && preg_match(SUPPORT_TYPES_REG,$_GET['type'])){
		$id = $_GET['id'];
		$type = $_GET['type'];
		$id_n_type = ['id'=>$id, 'type'=>$type];	
	}else{
				http_response_code(500);
				exit;
	}

	// Success ?
	try{

		// 3) Connect to Database
		$database = new Database();
		$connection = $database->connect();

		// 4) Read list 
		//  prepare model
		$itemx = new ItemX($connection, $id_n_type);
   	if(!$itemx->read()){
   		throw new Exception("Fail to read the item");
   	}
		$theItem = $itemx->container;// fetch the real obj
			
	}catch(Exception $e){
		http_response_code(500);
		exit;
	}
		// 5) Display: subitems: generate the tree to a finite level
		header("Content-type:text/html");
		genSubitemTo($theItem,1);
?>
