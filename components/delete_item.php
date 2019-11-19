<?php
/*
** delete item.
** // 1) Check authorization 
** // 2) Ajax?
** // 3) Get item id and type
** // 4) Connect to Database and prepare model
** // 5) delete the item
** // 6) Respond
*/
	include_once '../config/app_config.php';
	include_once '../config/Database.php';
	include_once '../models/ItemX.php';
	
	// 1) Check authorization 
	include '../authorize.php';// successfully sign in, $user_id, $username and $home_collection_id are set.
	// 2) Ajax?
	$ajax = isset($_POST['ajax']);
	
	try{
	 // 3) get data from query string $_GET
		if(isset($_GET['item_id']) && preg_match('/^[0-9]+$/',$_GET['item_id']) && isset($_GET['item_type']) && preg_match(SUPPORT_TYPES_REG,$_GET['item_type'])){
			$item_id= $_GET['item_id'];
			$item_type = $_GET['item_type'];
			$id_type_arr = ['id'=>$item_id, 'type'=>$item_type];
		}else{
			throw new Exception();
		}
	
		// 4) connect to DB
		$database = new Database();
		$connection = $database->connect();
			
		$item = new ItemX($connection,$id_type_arr);
		
	}catch(dbConnectException $dbe){
		if($ajax){
				header("Content-type:application/json");
				echo json_encode(["success"=>false,
														"message"=>$e->getMessage()]);
				http_response_code(500);
				exit;
		}else{
			$_SESSION['meassage'] = $e->getMessage();
			header("Location:../list.php");
		}
	}catch(Exception $e){
		if($ajax){
				http_response_code(400);
				header("Content-type:application/json");
				echo json_encode(["success"=>false,
														"message"=>$e->getMessage()]);
				exit;
		}else{
			$_SESSION['meassage'] = $e->getMessage();
			header("Location:../list.php");
		}
	}
	// 5) delete the item
	$result = $item->delete();
	// 6) Respond
	if($ajax){
	// request by ajax
		if($result){
			header("Content-type:application/json");
			echo json_encode(["success"=>true]);
		}else{
			echo json_encode(["success"=>false,
												"message"=>'Item is not deleted']);
		}
	}else{
	// request by normal 
		if($result){
			$_SESSION['message'] = 'Item is deleted';
		}else{
			$_SESSION['message'] = 'Item is not deleted';
		}
		header("Location:../list.php");
	}

?>
