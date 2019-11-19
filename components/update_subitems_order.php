<?php
/*
** update subitems(items) order under item(list).
** // 1) Check authorization 
** // 2) Get item id and type
** // 3) Get subitems order
** // 4) Connect to Database and prepare model
** // 5) Update order
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
		
		// 3) Get subitems order
		if(isset($_POST['item'])){
			$order = $_POST['item'];
		}else{
			throw new Exception("Order is not sent.");
		}
		
  	// 4) Connect to Database and prepare model
		$database = new Database();
		$connection = $database->connect();
		//use itemX
		$item = new ItemX($connection,$id_type_arr);
		
		// 5) Update order
		$item->setData('order',$order);
		$result = $item->updateOrder();
			
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
