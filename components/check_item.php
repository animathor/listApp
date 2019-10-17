<?php
	include_once '../config/app_config.php';
	include_once '../config/Database.php';
	include_once '../models/ItemX.php';
	include '../authorize.php';// successfully sign in, $user_id, $username and $home_collection_id are set.


	// get the item's id and type
	$item_types_reg = "/^".CHECK_TYPE."|".TASK_TYPE."$/";// the supported types which can be checked
	if(isset($_GET['item_id']) && preg_match('/^[0-9]+$/',$_GET['item_id']) && isset($_GET['item_type']) && preg_match($item_types_reg,$_GET['item_type'])){
		$item_id= $_GET['item_id'];
		$item_type = $_GET['item_type'];
		$id_type_arr = ['id'=>$item_id, 'type'=>$item_type];
		
		// get checkbox value
			if(isset($_POST['checked']) && $_POST['checked']=='true'){
				$checked_value = 1;
			}elseif(isset($_POST['checked']) && $_POST['checked']=='false'){ 
				$checked_value = 0;
			}
		//var_dump($checked_value);
		
		// connect to DB
		$database = new Database();
		$connection = $database->connect();

		// create itemX
		$item = new ItemX($connection, $id_type_arr);

		// call the check method
		// respond that it is success or not
		// request mode
		$result = $item->checkTheBox($checked_value);
		if(isset($_POST['ajax']) && $_POST['ajax']==true){
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
			header("Location:../list_template.php");
		}
		

	}
?>
