<?php
/*
** check item.
** // 1) Check authorization 
** // 2) Ajax?
** // 3) Get id and type
** // 4) Connect to Database and prepare model
** // 5) check item
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
		// 3) get the item's id and type
		if(isset($_GET['item_id']) && preg_match('/^[0-9]+$/',$_GET['item_id']) && isset($_GET['item_type']) && preg_match(SUPPORT_TYPES_REG,$_GET['item_type'])){
			$item_id= $_GET['item_id'];
			$item_type = $_GET['item_type'];
			$id_type_arr = ['id'=>$item_id, 'type'=>$item_type];
		}else{
			throw new Exception();
		}
		// get checkbox value
		if(isset($_POST['checked']) && $_POST['checked']=='true'){
			$checked_value = 1;
		}elseif(isset($_POST['checked']) && $_POST['checked']=='false'){ 
			$checked_value = 0;
		}
		
		// 4) connect to DB
		$database = new Database();
		$connection = $database->connect();

		// create itemX
		$item = new ItemX($connection, $id_type_arr);

	}catch(dbConnectException $dbe){
		if($ajax){
				http_response_code(500);
				exit;
		}else{
			$_SESSION['meassage'] = $e->getMessage();
			header("Location:../list.php");
		}
	}catch(Exception $e){
		if($ajax){
				header("Content-type:text/html");
				http_response_code(400);
				echo $e->getMessage();
				exit;
		}else{
			$_SESSION['meassage'] = $e->getMessage();
			header("Location:../list.php");
		}
	}
	// 5) call the check method
	$result = $item->checkTheBox($checked_value);
	
	// 6) Respond that it is success or not
	if($ajax){
		// by ajax
		header("Content-type: application/json");
		if($result){
			echo json_encode(['success'=>true,
												'checked'=>$checked_value]);
		}else{
			echo json_encode(['success'=> false,
												'message'=>"item is not checked"]);
		}		
	}else{
		// normal
		if(!$result){
			$_SESSION['massage']="fail to check the item";
		}
		header("Location:../list.php");
	}
?>
