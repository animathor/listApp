<?php
	include_once '../config/app_config.php';
	include_once '../config/Database.php';
	include_once '../models/ItemX.php';
	include '../authorize.php';// successfully sign in, $user_id, $username and $home_collection_id are set.
	//connect to DB
	$database = new Database();
	$connection = $database->connect();

	//get data from query string $_GET
		if(isset($_GET['item_id']) && preg_match('/^[0-9]+$/',$_GET['item_id']) && isset($_GET['item_type']) && preg_match(SUPPORT_TYPES_REG,$_GET['item_type'])){
		$item_id= $_GET['item_id'];
		$item_type = $_GET['item_type'];
		$id_type_arr = ['id'=>$item_id, 'type'=>$item_type];
		//use itemX
		$item = new ItemX($connection,$id_type_arr);
		//bind data
					
			//due prepare
			$today = date("Y-m-d", time());
			if(!empty($_POST['dueTime']) && !empty($_POST['dueDate'])){
				$due_value = $_POST['dueDate'].' '.$_POST['dueTime'];
			}elseif(!empty($_POST['dueDate'])){
				$due_value = $_POST['dueDate'].' 23:59:59';//default is the end of the day if the date is set
			}elseif(!empty($_POST['dueTime'])){
				$due_value = $today.' '.$_POST['dueTime'];//default is today if the time is set
			}else{
				$due_value=null;
			}

			//schedule prepare
			if(!empty($_POST['scheduleTime']) && !empty($_POST['scheduleDate'])){
				$schedule_value = $_POST['scheduleDate'].' '.$_POST['scheduleTime'];
			}elseif(!empty($_POST['scheduleDate'])){
				$schedule_value = $_POST['scheduleDate'].' 00:00:00';//default is the begin of the day if the date is set
			}elseif(!empty($_POST['scheduleTime'])){
				$schedule_value = $today.' '.$_POST['scheduleTime'];//default is today if the time is set
			}else{
				$schedule_value=null;
			}

			
		$title = empty($_POST['title'])? 'blank' : htmlspecialchars($_POST['title']);
		$note = htmlspecialchars($_POST['note']);
		switch($item_type){
			case 2:
				$item->setData('title',$title);
				$item->setData('note',$note);
				break;
			case 4:
				$item->setData('title',$title);
				$item->setData('note',$note);
				break;
			case 6:
				$item->setData('title',$title);
				$item->setData('note',$note);	
				$item->setData('schedule',$schedule_value);
				$item->setData('due',$due_value);
				break;
		}
		$result = $item->update();
		if(isset($_POST['ajax'])){
		// request by ajax
			if($result){
				header("Content-type:application/json");
				echo json_encode(["success"=>true,
														"title"=>$title,
														"note"=>$note]);
			}else{
				header("Content-type:application/json");
				echo json_encode(["success"=>false,
														"message"=>"item is not updated"]);
			}
		}else{
			if($result){
				$_SESSION['message'] = 'item'.$item_id.'is updated';
			}else{
				$_SESSION['message'] = 'item'.$item_id.' is not updated';
			}
			header("Location:../list_template.php");
		}
		
	}
?>
