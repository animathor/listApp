<?php
	include_once '../config/Database.php';
	include_once '../models/Collections.php';
	include_once '../models/Items_obj.php';


	//connect to DB
	$database = new Database();
	$connection = $database->connect();
	var_dump($connection);
	// get type from $_POST
	if(isset($_POST['list_type'])){
		$list_type = $_POST['list_type'];
	}
	// create list
		$newList = new ItemX($connection,['type'=>$list_type]);
		echo '<pre>';
		echo var_dump($newList);
		echo "</pre>";
		session_start();
		if($newList->create()){
			$id = $newList->id;
			header("Location:list_template.php?id=".$id);
		}else{
			header("Location:collection_template.php");
			
		}
	}
?>
