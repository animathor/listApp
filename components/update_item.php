<?php
	include_once '../config/app_config.php';
	include_once '../config/Database.php';
	include_once '../models/ItemX.php';
	include '../authorize.php';// successfully sign in, $user_id, $username and $home_collection_id are set.
	//connect to DB
	$database = new Database();
	$connection = $database->connect();
	var_dump($connection);echo "<br/>";

	//get data from query string $_GET
$item_types_reg = "/^".ITEM_TYPE."|".CHECK_TYPE."|".TASK_TYPE."$/";// supported types
		if(isset($_GET['item_id']) && preg_match('/^[0-9]+$/',$_GET['item_id']) && isset($_GET['item_type']) && preg_match($item_types_reg,$_GET['item_type'])){
		$item_id= $_GET['item_id'];
		$item_type = $_GET['item_type'];
		$id_type_arr = ['id'=>$item_id, 'type'=>$item_type];
		//use itemX
		$item = new ItemX($connection,$id_type_arr);
		echo '<pre>';
		echo var_dump($item);
		echo "</pre>";
		//bind data
			//checked prepare
			if(isset($_POST['checked']) && $_POST['checked']==true){
				$checked_value = 1;
			}else{
				$checked_value = 0;
			}
			echo 'chk_V:'.$checked_value."<br/>";
			
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
			echo 'due_V:'.$due_value."<br/>";
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
			echo 'schedule_V:'.$schedule_value."<br/>";
			
		print_r($_POST);
		switch($item_type){
			case 2:
				$item->setData('title',$_POST['title']);
				$item->setData('note',$_POST['note']);
				break;
			case 4:
				$item->setData('title',$_POST['title']);
				$item->setData('note',$_POST['note']);
				break;
			case 6:
				$item->setData('title',$_POST['title']);
				$item->setData('note',$_POST['note']);	
				$item->setData('schedule',$schedule_value);
				$item->setData('due',$due_value);
				break;
		}
		echo '<pre>';
		echo var_dump($item);
		echo "</pre>";
		
		session_start();
		if($item->update()){
			$_SESSION['message'] = 'item'.$item_id.'updated';
		}else{
			$_SESSION['message'] = 'item'.$item_id.' not updated';
		}
		header("Location:../list_template.php");
	}
?>
