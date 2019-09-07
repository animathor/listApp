<?php
	include_once '../config/Database.php';
	include_once '../models/Collections.php';
	include_once '../models/Items_obj.php';
	
	function genSubCollTo($collection, $level){
		// add new collection
		echo '<form action="add_new_collection.php?id='.$collection->id.'" method="post">'.
						'<input type="text" name="collection_title" placeholder="add new collection"><br />'.
					'</form>';
		// the number of levels to show
		if($level == 0){
			return;
		}
		// asdf
		else if($collection->readAllSub() && !empty($collection->subItems)){
			echo '<ul>';
			foreach($collection->subItems as $subEle){
				echo '<li>';
				switch($subEle->type){
					// collection
					case 1:
						echo '<a href="collection_template.php?id='.$subEle->id.'">'.$subEle->title.'</a>';
						echo '<form action="update_collection.php?id='.$subEle->id.'" method="post">'.
									'<input type="text" name="collection_title" value="'.$subEle->title.'">'.
								'</form>';
						echo '<form action="add_new_list.php?id='.$subEle->id.'" method="post">'.
										'<select name="list_type">'.
										'<option value=2>item</option>'.
										'<option value=4>check</option>'.
										'<option value=6>task</option></select>'.
									'<input type="submit" value="add">'.
									'</form>';// Add list link
						echo '<a href="delete_collection.php?id='.$subEle->id.'">X</a>';// delete link

						// go next level, read the subEles
						if($subEle->read()){
							genSubCollTo($subEle, $level-1);
						}
						break;
					// item
					case 2:
						echo '<a href="list_template.php?id='.$subEle->id.'&type=2">'.$subEle->title.'</a>';
						echo '<a href="delete_list.php?id='.$collection->id.'&list_id='.$subEle->id.'">X</a>';// delete list
						break;
					// check
					case 4:
						echo '<a href="list_template.php?id='.$subEle->id.'&type=4">'.$subEle->title.'</a>';
						echo '<a href="delete_list.php?id='.$collection->id.'&list_id='.$subEle->id.'">X</a>';// delete list
						break;
					// task
					case 6:
						echo '<a href="list_template.php?id='.$subEle->id.'&type=6">'.$subEle->title.'</a>';
						echo '<a href="delete_list.php?id='.$collection->id.'&list_id='.$subEle->id.'">X</a>';// delete list
						break;
				}
				echo '</li>';
			}
			echo '</ul>';
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
	</head>
</html>
<body>
	
<?php
	// Connect to database	
	$database = new Database();
	$connection = $database->connect();
	
	session_start();

	// The default page shows all collections with no parent
	// Use query string to store collection id for browsing specific id collection page
		// Get the id to present that page

		$defualt_id = 1;// one user one root collection 
		$collection = new Collection($connection);// Create a model for reading the data
		
		// 1. get 2. current 3. default
		if(isset($_GET['id'])){
			$collection->id = $_GET['id'];
		}else if(isset($_SESSION['current_collection'])){
			$collection->id = $_SESSION['current_collection'];
		}else{
			// read default collection
			$collection->id = $defualt_id;
		}
		
		if(!$collection->read()){
			// since the current collection can't be deleted, and it always start from default.
			// Current collection always exist.
			header("Location:collection_template.php");// reload
		}
		
		// note down the current collection

		$_SESSION['current_collection'] = $collection->id;
		
		// Generate the page
		// current collection
			echo '<h1>'.$collection->title.'</h1>';
			if($collection->id != $defualt_id){
				echo '<form action="add_new_list.php?id='.$collection->id.'" method="post">'.
							'<select name="list_type">'.
								'<option value=2>item</option>'.
								'<option value=4>check</option>'.
								'<option value=6>task</option>'.
							'</select>'.
							'<input type="submit" value="add">'.
						'</form>';// Add list link
			}
		// operation message
			if(isset($_SESSION['message'])){
				echo "<h2>".$_SESSION['message']."</h2>";
				unset($_SESSION['message']);
			}
		
		// show path
		$collection_train = $collection->traceBack();
		$link_train = $collection->title;
		foreach($collection_train as $c){
			$link_train = '<a href=collection_template.php?id='.$c->id.'>'.$c->title.'</a> /'.$link_train;
		}
		echo $link_train;	
		
		// subcollections and subitem(list)
			genSubCollTo($collection, 5);

?>
</body>
