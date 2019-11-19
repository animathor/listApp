<?php
/*
** Read collection and Display it.
** // 1) Check authorization 
** // 2) Get id and type 
** //			id: check int, type: check item type
** // 3) Connect to Database 
** //		  successfully?
** // 4) Read collection ($collection)
** //		  successfully?
** // 5) Display: collection title and $collection edit form
** // 6) Display: navigator: link to supcollections
** // 7) Display: subcollections and lists: generate the tree to a finite level
*/

	include_once 'config/app_config.php';
	include_once 'config/Database.php';
	include_once 'models/Collections.php';
	include 'collection_display.php';// function: genSubCollTo

	// 1) Session start and check the authorization
	include 'authorize.php';// successfully sign in, $user_id, $username and $home_collection_id are set.
	
		// Get the id
		
		// 1. get 2. current 3. default
		if(isset($_GET['id']) && preg_match('/^[0-9]+$/',$_GET['id'])){
			$collection_id = $_GET['id'];
		}else if(isset($_SESSION['current_collection'])){
			$collection_id = $_SESSION['current_collection'];
		}else{
			// read default collection
			$collection_id = $home_collection_id;
		}

	// Success ?
	try{

		// 3) Connect to Database
		$database = new Database();
		$connection = $database->connect();

		// 4) Read collection
		$collection = new Collection($connection);// Create a model for reading the data
		$collection->id = $collection_id;
		if(!$collection->read()){
			// since the current collection can't be deleted, and it always start from home collection.
			// Current collection always exist.
			throw new Exception("Fail to read the collection");
		}

		// check if the collection belons to the current user
		if($collection->author_id != $user_id){
			throw new Exception("No such collection");
		}
		
		$_SESSION['current_collection'] = $collection->id;// note down the current collection
			
	}catch(dbConnectException $dbe){
		$_SESSION['message'] = $dbe->getMessage();
		header("Location:error.php");
	}catch(Exception $e){
		$_SESSION['message'] = $e->getMessage();
		header("Location:collection.php?id=".$home_collection_id);// reload
	}

?>

<!--collection template-->
<!Doctype html>
<html>
	<head>
		<meta charset="utf-8"/>
		<title>collection</title>
		<link href="css/header.css" type="text/css" rel="stylesheet"/>
		<link href="css/collection.css" type="text/css" rel="stylesheet"/>
	</head>
	<body>
<?php

		// Generate the page
		include_once 'header.php';
		// 5) Display: collection title and $collection edit form		
		// current collection
		$collection_title = htmlspecialchars($collection->title);
		echo '<h2>'.$collection_title.'</h2>';

		// 6) Display: navigator: link to supcollections
		echo '<nav>';
			// show path
			$collection_train = $collection->traceBack();
			$link_train = '<li id="nav-current-collection">'.$collection_title.'</li>';
			foreach($collection_train as $c){
				$c_title = htmlspecialchars($c->title);
				$link_train = '<a href=collection.php?id='.$c->id.'>'.$c_title.'</a> /'.$link_train;
			}
				
					echo '<form id="current-coll-addList" action="components/add_new_list.php?id='.$collection->id.'" method="post">'.
								'<select name="list_type">'.
									'<option value='.ITEM_TYPE.'>item</option>'.
									'<option value='.CHECK_TYPE.'>check</option>'.
									'<option value='.TASK_TYPE.'>task</option>'.
								'</select>'.
								'<input type="submit" value="add">'.
							'</form>';// Add list link
			echo '<ul>'.$link_train.'</ul>';
		echo '</nav>';

		// response message
		$massage = '';
		if(isset($_SESSION['message'])){
			$massage = $_SESSION['message'];
			unset($_SESSION['message']);
		}
		echo "<h3 id='message-board'>".$massage."</h3>";
		
		// 7) Display: subcollections and lists: generate the tree to a finite level
		echo "<div id='subEles'>";
			genSubCollTo($collection, LEVEL_OF_COLLECTION);
		echo "</div>";

?>
<script src="script/jquery.js"></script>
<script src="script/jquery-ui.min.js"></script>
<script src="script/utilities.js"></script>
<script src="script/control.js"></script>
<script src="script/collection_control.js"></script>
<script src="script/sign_in_again.js"></script>
</body>
</html>
