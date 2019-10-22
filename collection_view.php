<?php
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

	function genAddNewColl($collection){
	echo '<form class="add-new-collection" action="components/add_new_collection.php?id='.$collection->id.'" method="post">'.
						'<input type="text" name="collection_title" placeholder="add new collection"><br />'.
					'</form>';
	}
	
	function genSubCollTo($collection, $level){
		
		// the number of levels to show
		if($level == 0){
			return;
		}else{
			// add new collection input
			genAddNewColl($collection);
			echo '<ul>';
			if($collection->readAllSub() && !empty($collection->subItems)){
				
				foreach($collection->subItems as $subEle){
					echo '<li>';
					genOneEle($collection,$subEle);// html
					// if it is collection, then go to next level.
					if($subEle->type == 1 && $subEle->read()){
								genSubCollTo($subEle, $level-1);
					}
					echo '</li>';
				}
			}
			echo '</ul>';
		}
		return;
	}
?>
