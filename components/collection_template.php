<?php
	include_once '../config/Database.php';
	include_once '../models/Collections.php';
	include_once '../models/Items_obj.php';
	
	function genSubCollTo($collection, $level){
		// add new collection
		echo '<form action="add_new_collection.php?id='.$collection->id.'" method="post">'.
						'<input type="text" name="collection_title" placeholder="add new collection"><br />'.
					'</form>';

		if($level == 0){
			return;
		}
		else if($collection->readLists()){
			echo '<ul>';
			foreach($collection->subItems as $subEle){
				echo '<li>';
				switch($subEle->type){
					// collection
					case 1:
						echo '<form action="update_collection.php?id='.$subEle->id.'" method="post">'.
									'<input type="text" name="collection_title" value="'.$subEle->title.'">'.
								'</form>';
						echo '<form action="add_new_list.php?id ='.$subEle->id.'" method="post">'.
										'<select name="list_type"><option value=6>task</option></select>'.
									'<input type="submit" value="add">'.
									'</form>';// Add list link
						echo '<a href="delete_collection.php?'.$subEle->id.'">X</a>';// delete link

						// go next level, read the subEles
						if($subEle->read()){
							genSubCollTo($subEle->subItems, $level-1);
						}
						break;
					// item
					case 2:
						echo '<a href="list_template.php?id='.$subEle->id.'&type=2">'.$subEle->title.'</a>';
						break;
					// check
					case 4:
						echo '<a href="list_template.php?id='.$subEle->id.'&type=4">'.$subEle->title.'</a>';
						break;
					// task
					case 6:
						echo '<a href="list_template.php?id='.$subEle->id.'&type=6">'.$subEle->title.'</a>';
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
			echo '<a href="add_new_item.php?id='.$collection->id.'"> + list</a>';// Add list link
		// operation message
			if(isset($_SESSION['message'])){
				echo "<h2>".$_SESSION['message']."</h2>";
				unset($_SESSION['message']);
			}
			genSubCollTo($collection, 2);// subcollections and subitem(list)

?>
</body>
