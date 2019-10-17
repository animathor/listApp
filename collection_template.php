<?php
	include_once 'config/Database.php';
	include_once 'models/Collections.php';
	include_once 'models/Items_obj.php';
	include 'authorize.php';// successfully sign in, $user_id, $username and $home_collection_id are set.
		
	function genOneEle($collection,$subEle){
				$subEle_title = htmlspecialchars($subEle->title);
				switch($subEle->type){
					// collection
					case 1:
						echo '<div class="collection">';
						echo 	'<div class="collection-edit">';
						echo 	'<div class="collection-control">';
						echo '<img class="edit-button" src="img/edit.png"/>';
						echo '<form action="components/add_new_list.php?id='.$subEle->id.'" method="post">'.
										'<select name="list_type">'.
										'<option value=2>item</option>'.
										'<option value=4>check</option>'.
										'<option value=6>task</option></select>'.
									'<input type="submit" value="+">'.
									'</form>';// Add list link
						echo '<a href="components/delete_collection.php?id='.$subEle->id.'">&cross;</a>';// delete link
						echo  '</div>';
						echo 		'<a class="title-link" href="collection_template.php?id='.$subEle->id.'">'.$subEle_title.'</a>';
						echo 		'<form class="edit-title" action="components/update_collection.php?id='.$subEle->id.'" method="post">'.
											'<input type="text" name="collection_title" value="'.$subEle_title.'">'.
										'</form>';
						echo  '</div>';
						
						echo '</div>';
						break;
					// item
					case 2:
						echo '<div class="item">';
						echo '<span class="type">item</span>';
						echo '<a href="list_template.php?id='.$subEle->id.'&type=2">'.$subEle_title.'</a>';
						echo '<a class="list-control" href="components/delete_list.php?id='.$collection->id.'&list_id='.$subEle->id.'">&cross;</a>';// delete list
						echo '</div>';
						break;
					// check
					case 4:
						echo '<div class="check">';
						echo '<span class="type">check</span>';
						echo '<a href="list_template.php?id='.$subEle->id.'&type=4">'.$subEle_title.'</a>';
						echo '<a class="list-control" href="components/delete_list.php?id='.$collection->id.'&list_id='.$subEle->id.'">&cross;</a>';// delete list
						echo '</div>';
						break;
					// task
					case 6:
						echo '<div class="task">';
						echo '<span class="type">task</span>';
						echo '<a href="list_template.php?id='.$subEle->id.'&type=6">'.$subEle_title.'</a>';
						echo '<a class="list-control" href="components/delete_list.php?id='.$collection->id.'&list_id='.$subEle->id.'">&cross;</a>';// delete list
						echo '</div>';
						break;
				}
	}
	
	function genSubCollTo($collection, $level){
		
		// the number of levels to show
		if($level == 0){
			return;
		}
		else{
		// add new collection input
		echo '<form class="add-new-collection" action="components/add_new_collection.php?id='.$collection->id.'" method="post">'.
						'<input type="text" name="collection_title" placeholder="add new collection"><br />'.
					'</form>';
			if($collection->readAllSub() && !empty($collection->subItems)){
				echo '<ul>';
				foreach($collection->subItems as $subEle){
					echo '<li>';
					genOneEle($collection,$subEle);// html
					// if it is collection, then go to next level.
					if($subEle->type == 1 && $subEle->read()){
								genSubCollTo($subEle, $level-1);
					}
					echo '</li>';
				}
				echo '</ul>';
			}
		}
		return;
	}
?>

<!--collection template-->
<!Doctype html>
<html>
	<head>
		<meta charset="utf-8"/>
		<title>file</title>
		<link href="css/header.css" type="text/css" rel="stylesheet"/>
		<link href="css/collection.css" type="text/css" rel="stylesheet"/>
	</head>
</html>
<body>
					
<?php	
	// Connect to database	
	$database = new Database();
	$connection = $database->connect();

	// The default page shows all collections with no parent
	// Use query string to store collection id for browsing specific id collection page
		// Get the id to present that page

		$collection = new Collection($connection);// Create a model for reading the data
		
		// 1. get 2. current 3. default
		if(isset($_GET['id']) && preg_match('/^[0-9]+$/',$_GET['id'])){
			$collection->id = $_GET['id'];
		}else if(isset($_SESSION['current_collection'])){
			$collection->id = $_SESSION['current_collection'];
		}else{
			// read default collection
			$collection->id = $home_collection_id;
		}
		
		if(!$collection->read()){
			// since the current collection can't be deleted, and it always start from default.
			// Current collection always exist.
			$_SESSION['message'] = 'Fail to read the collection';
			header("Location:collection_template.php?id=".$home_collection_id);// reload
			exit;
		}
		// check if the collection belons to the current user

		if($collection->author_id != $user_id){
			$_SESSION['message'] = 'No such collection';
			header("Location:collection_template.php");
			exit;
		}
		
		// note down the current collection

		$_SESSION['current_collection'] = $collection->id;
		

		// Generate the page
		include_once 'header.php';
		// current collection
		$collection_title = htmlspecialchars($collection->title);
		echo '<h2>'.$collection_title.'</h2>';
		// navigator
		echo '<nav>';
			// show path
			$collection_train = $collection->traceBack();
			$link_train = '<span id="nav-current-collection">'.$collection_title.'</span>';
			foreach($collection_train as $c){
				$c_title = htmlspecialchars($c->title);
				$link_train = '<a href=collection_template.php?id='.$c->id.'>'.$c_title.'</a> /'.$link_train;
			}
			echo '<div>'.$link_train.'</div>';	
					echo '<form id="current-coll-addList" action="components/add_new_list.php?id='.$collection->id.'" method="post">'.
								'<select name="list_type">'.
									'<option value=2>item</option>'.
									'<option value=4>check</option>'.
									'<option value=6>task</option>'.
								'</select>'.
								'<input type="submit" value="add">'.
							'</form>';// Add list link
		echo '</nav>';
		// operation message
			if(isset($_SESSION['message'])){
				echo "<h3>".$_SESSION['message']."</h3>";
				unset($_SESSION['message']);
			}
		echo "<div id='subele'>";
			
		// subcollections and subitem(list)
			genSubCollTo($collection, 5);
		echo "</div>";		
?>
<script src="script/hide.js"></script>
<script src="script/collection_hide.js"></script>
</body>
