<?php
/*
** Add new item(subitem) to list(item).
** // 1) Check authorization 
** // 2) Ajax?
** // 3) Get list id and type
** // 4) Connect to Database 
** // 5) Validate title
** // 6) Read list and add new item
** // 7) Respond
*/
	include_once '../config/app_config.php';
	include_once '../config/Database.php';
	include_once '../models/ItemX.php';
	include '../item_view.php';// for function "genOneItem($item,$isItem)" and "genSubitemTo($item, $level)"
	
	// 1) Check authorization 
	include_once '../authorize.php';// successfully sign in, $user_id, $username and $home_collection_id are set.
	// 2) Ajax?
	$ajax = isset($_POST['ajax']);

	try{
		
		// 3) get list id and type
			if(isset($_GET['list_id']) && preg_match('/^[0-9]+$/',$_GET['list_id']) && isset($_GET['list_type']) && preg_match(SUPPORT_TYPES_REG,$_GET['list_type'])){
			$list_id = $_GET['list_id'];
			$list_type = $_GET['list_type'];
			$id_n_type = ['id'=>$list_id, 'type'=>$list_type];
		}else{
			throw new Exception();
		}
		
		// 4) connect to DB
		$database = new Database();
		$connection = $database->connect();
		
		// create list
		$currentList = new ItemX($connection, $id_n_type);
		
		// 5) Validate title
		//get title from $_POST
		if(isset($_POST['item_title'])){
			if(strlen($_POST['item_title'])==0){
				throw new Exception('Please enter something');
			}else if(strlen($_POST['item_title'])>255){
				throw new Exception('Title should be at most 255 charactors');
			}
			$item_title = $_POST['item_title'];
		}
	}catch(dbConnectException $dbe){
		if($ajax){
				http_response_code(500);
				exit;
		}else{
			$_SESSION['meassage'] = $e->getMessage();
			header("Location:../list_template.php");
		}
	}catch(Exception $e){
		if($ajax){
				header("Content-type:text/html");
				http_response_code(400);
				echo $e->getMessage();
				exit;
		}else{
			$_SESSION['meassage'] = $e->getMessage();
			header("Location:../list_template.php");
		}
	}
	// 6) Read list and add new item
	// author
	$author_id = $user_id;// $user_id store in authorize.php
	$newSubItem_id=$result=$currentList->addnewSubItem($author_id, $item_title);
	
	// 7) Respond
	if($ajax){
	// request by ajax
		if($result){
			$newSubItemX = new ItemX($connection, ["id"=>$newSubItem_id]);
			$newSubItemX->read();
			$newSubItem = $newSubItemX->container;
			header("Content-type:text/html");
			echo '<li id="item_'.$newSubItem->id.'" data-id="'.$newSubItem->id.'" data-type="'.$newSubItem->type.'">';
				genOneItem($newSubItem, true);
				genAddNew($newSubItem);
				echo '<ul class="subitems hide" data-id="'.$newSubItem->id.'" data-type="'.$newSubItem->type.'"></ul>';
			echo '</li>';
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
?>
