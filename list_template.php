<?php
/*
** Read list and Display it.
** // 1) Check authorization 
** // 2) Get id and type 
** //			id: check int, type: check item type
** // 3) Connect to Database 
** //		  successfully?
** // 4) Read list ($theList)
** //		  successfully?
** // 5) Display: list title and $theList edit form
** // 6) Display: navigator: link to collection and supitems (trace back to the real list head(root item))
** // 7) Display: subitems: generate the tree to a finite level
*/

	include_once 'config/app_config.php';
	include_once 'config/Database.php';
	include_once 'models/Items_obj.php';
	include 'item_view.php';// for function "genEditForm($item,$isItem)" and "genSubitemTo($item, $level)"

	// 1) Session start and check the authorization
	include 'authorize.php';// successfully sign in, $user_id, $username and $home_collection_id are set.
	
	// 2) Get id and type
	//: --$_GET (id, type) --back to previous list --back to collection
	$item_types_reg = "/^".ITEM_TYPE."|".CHECK_TYPE."|".TASK_TYPE."$/";// supported types
	if(isset($_GET['id']) && preg_match('/^[0-9]+$/',$_GET['id']) && isset($_GET['type']) && preg_match($item_types_reg,$_GET['type'])){
		$id = $_GET['id'];
		$type = $_GET['type'];
		$id_n_type = ['id'=>$id, 'type'=>$type];	
	}else if(isset($_SESSION['current_list'])){
		$id_n_type = $_SESSION['current_list'];
	}else{
		header("Location:collection_template.php");
		exit;
	}

	// Success ?
	try{

		// 3) Connect to Database
		$database = new Database();
		$connection = $database->connect();

		// 4) Read list 
		//  prepare model
		$itemx = new ItemX($connection, $id_n_type);
   	if(!$itemx->read()){
   		throw new Exception("Fail to read the item");
   	}
		$theList = $itemx->container;// fetch the real obj
		$_SESSION['current_list'] = $id_n_type;// note down the current list
			
	}catch(dbConnectException $dbe){
		$_SESSION['message'] = $dbe->getMessage();
		header("Location:error.php");
	}catch(Exception $e){
		$_SESSION['message'] = $e->getMessage();
		header("Location:list_template.php");
	}

?>

<!--list template-->
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

		// Generate the page
		include_once 'header.php';
		// 5) Display: list title and $theList edit form
		echo '<h2 id="list-title">'.$theList->title.'</h2>';
		genEditForm($theList, false);
		
		// 6) Display: navigator: link to collection and supitems
		echo '<nav>';
		// show path
		$item_train = $theList->traceBack();// supitems
		$link_train = '<li id="nav-current-item">'.$theList->title.'</li>';
		foreach($item_train as $item){
		$item_title = $item->title;
			$link_train = '<li><a href=list_template.php?id='.$item->id.'&type='.$item->type.'>'.$item_title.'</a> /</li>'.$link_train;
		}
		// link Back to collection
		if(empty($item_train)){
			$item_train[] = $theList;
		}
		if($collection = end($item_train)->in_collection()){
		echo '<span><a href=collection_template.php?id='.$collection->id.'>Back to '.$collection->title.'</a></span><br/>';
		}else{
			
		}
		echo "<ul>{$link_train}</ul>";
		echo '</nav>';

		// response message
		$massage = '';
		if(isset($_SESSION['message'])){
			$massage = $_SESSION['message'];
			unset($_SESSION['message']);
		}
		echo "<h3 id='message-board'>".$massage."</h3>";
		
		// 7) Display: subitems: generate the tree to a finite level
		echo '<div id="subitems">';
		genSubitemTo($theList, LEVEL_OF_LIST);
		echo '</div>';
?>
	<script src="script/utilities.js"></script>
	<script src="script/control.js"></script>
	<script src="script/list_control.js"></script>
	</body>
</html>
