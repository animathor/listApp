<?php
/*
** Move subitem from one item(list) to another.
** // 1) Check authorization 
** // 2) Get item id and type
** // 3) Get draggedSubItemId, formerParentId, subitems order in new list(item)
** // 4) Connect to Database and prepare model
** // 5) Move subitem
** // 6) Respond
*/
	include_once '../config/app_config.php';
	include_once '../config/Database.php';
	include_once '../models/ItemX.php';
	
	// 1) Check authorization 
	include '../authorize.php';// successfully sign in, $user_id, $username and $home_collection_id are set.
	
	try{
		// 2) Get item id and type
		if(isset($_GET['item_id']) && preg_match('/^[0-9]+$/',$_GET['item_id']) && isset($_GET['item_type']) && preg_match(SUPPORT_TYPES_REG,$_GET['item_type'])){
		$item_id= $_GET['item_id'];
		$item_type = $_GET['item_type'];
		$id_type_arr = ['id'=>$item_id, 'type'=>$item_type];
		}else{
			throw new Exception();
		}
		
		// 3) Get draggedSubItemId, formerParentId, subitems order in new list(item)
			$draggedSubItemId = filter_input(INPUT_POST,'draggedSubItemId', FILTER_VALIDATE_INT);
			$formerParentId = filter_input(INPUT_POST,'formerParentId', FILTER_VALIDATE_INT);
			$order = (isset($_POST['item'])) ? $_POST['item'] : false;
		if(!$order || !$draggedSubItemId || !$formerParentId){
			throw new Exception("Data is not sent.");
		}
		
  	// 4) Connect to Database and prepare model
		$database = new Database();
		$connection = $database->connect();
		//use itemX
		$item = new ItemX($connection,$id_type_arr);
		
		// 5) Move subitem
		$item->setData('order',$order);
		$result = $item->moveSubItem($formerParentId, $draggedSubItemId);
			
	}catch(dbConnectException $dbe){
		header("Content-type:application/json");
		echo json_encode(["success"=>false,
												"message"=>$e->getMessage()]);
		http_response_code(500);
		exit;
	}catch(Exception $e){
		http_response_code(400);
		header("Content-type:application/json");
		echo json_encode(["success"=>false,
		"line"=>$e->getLine(),"code"=>$e->getCode(),
												"message"=>$e->getMessage()]);
		exit;
	}
	
 	// 6) Respond
	if($result){
		header("Content-type:application/json");
		echo json_encode(["success"=>true]);
	}else{
		header("Content-type:application/json");
		echo json_encode(["success"=>false]);
	}
?>
