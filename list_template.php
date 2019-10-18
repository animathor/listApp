<?php
	include_once 'config/app_config.php';
	include_once 'config/Database.php';
	include_once 'models/Items_obj.php';
	include_once 'authorize.php';// successfully sign in, $user_id, $username and $home_collection_id are set.
	include 'item_view.php';// for function "genEditForm($item,$isItem)" and "genSubitemTo($item, $level)"

	
	// Connect to database	
	$database = new Database();
	$connection = $database->connect();
	
	
	// read list
	$item_types_reg = "/^".ITEM_TYPE."|".CHECK_TYPE."|".TASK_TYPE."$/";// supported types
		if(isset($_GET['id']) && preg_match('/^[0-9]+$/',$_GET['id']) && isset($_GET['type']) && preg_match($item_types_reg,$_GET['type'])){
			$id = $_GET['id'];
			$type = $_GET['type'];
			$id_n_type = ['id'=>$id, 'type'=>$type];	
		}else	if(isset($_SESSION['current_list'])){
			$id_n_type = $_SESSION['current_list'];
		}else{
				header("Location:collection_template.php");
				exit;
		}
			// prepare model
		$itemx = new ItemX($connection, $id_n_type);
		$theList = $itemx->container;// fetch the real obj

		if($theList->read()){
			// note down the current list
				$_SESSION['current_list'] = $id_n_type;
			
		}else{
			header("Location:collection_template.php");
			exit;
		}

?>

<!Doctype html>
<html>
	<head>
		<meta charset="utf-8"/>
		<title>list</title>
		<link href="css/header.css" type="text/css" rel="stylesheet"/>
		<link href="css/list.css" type="text/css" rel="stylesheet">
	</head>
<body>

<?php

		include_once 'header.php';
			// display list
		echo '<h2 id="list-title">'.$theList->title.'</h2>';

		genEditForm($theList, false);
		
		// navigator
		echo '<nav>';
		// show path
		$item_train = $theList->traceBack();//supitems
		$link_train = '<li id="nav-current-item">'.$theList->title.'</li>';
		foreach($item_train as $item){
		$item_title = $item->title;
			$link_train = '<li><a href=list_template.php?id='.$item->id.'&type='.$item->type.'>'.$item_title.'</a> /</li>'.$link_train;
		}
		// link Back to collection
		if(empty($item_train)){
			$item_train[] = $theList;
		}
		$collection = end($item_train)->in_collection();
		echo '<span><a href=collection_template.php?id='.$collection->id.'>Back to '.$collection->title.'</a></span><br/>';
		echo "<ul>{$link_train}</ul>";
		echo '</nav>';
		// responce	msg
		$massage = '';
		if(isset($_SESSION['message'])){
			$massage = $_SESSION['message'];
			unset($_SESSION['message']);
		}
		echo "<h3 id='message-board'>".$massage."</h3>";
		
		// generate subitems
		echo '<div id="subitems">';
		genSubitemTo($theList, 4);
		echo '</div>';
//
?>
<script src="script/hide.js"></script>
<script src="script/list_hide.js"></script>
<script src="script/list_control.js"></script>
</body>
