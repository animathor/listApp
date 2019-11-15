<?php
	function displayListEle($type, $type_num, $list_id, $list_title, $collection_id){
		echo '<div class="list">';
			echo '<span class="type">'.$type.'</span>';
				echo '<a href="list_template.php?id='.$list_id.'&type='.$type_num.'">'.$list_title.'</a>';
				echo '<div class="control">';
				echo '<a class="delete-button" href="components/delete_list.php?id='.$collection_id.'&list_id='.$list_id.'">&cross;</a>';// delete list
			echo '</div>';
		echo '</div>';
	}
	
	function genOneEle($collection,$subEle){
		$subEle_title = htmlspecialchars($subEle->title);
		switch($subEle->type){
			// collection
			case COLLECTION_TYPE:
				echo '<div class="collection">';
				echo 	'<div class="control">';
					echo '<div class="edit-button"></div>';//'<img class="edit-button" src="img/edit.png"/>';
					echo '<form action="components/add_new_list.php?id='.$subEle->id.'" method="post">'.
									'<select name="list_type">'.
									'<option value='.ITEM_TYPE.'>item</option>'.
									'<option value='.CHECK_TYPE.'>check</option>'.
									'<option value='.TASK_TYPE.'>task</option></select>'.
								'<input type="submit" value="+">'.
								'</form>';// Add list link
					echo '<a class="delete-button" href="components/delete_collection.php?id='.$subEle->id.'">&cross;</a>';// delete link
				echo  '</div>';
				echo 		'<form class="edit-form" action="components/update_collection.php?id='.$subEle->id.'" method="post">'.
									'<a class="title-link" href="collection_template.php?id='.$subEle->id.'">'.$subEle_title.'</a>'.
									'<input class="edit-title" type="text" name="title" value="'.$subEle_title.'">'.
								'</form>';

				
				echo '</div>';
				break;
			// item
			case ITEM_TYPE:
				displayListEle('item', ITEM_TYPE, $subEle->id, $subEle_title, $collection->id);
				break;
			// check
			case CHECK_TYPE:
				displayListEle('check', CHECK_TYPE, $subEle->id, $subEle_title, $collection->id);
				break;
			// task
			case TASK_TYPE:
				displayListEle('task', TASK_TYPE, $subEle->id, $subEle_title, $collection->id);
				break;
		}//end switch
		
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
			if($collection->readAllSub()){
				// display subcollections
				echo '<ul class="subcollections">';
				if(!empty($collection->subCollections)){
					foreach($collection->subCollections as $subCollection){
							// may has next level items, load by ajax
							if($level == 1){
								echo '<li class="load-more" data-id="'.$subCollection->id.'" >';
							}else{
								echo "<li>";
							}
							genOneEle($collection,	$subCollection);// html
							// if it is collection, then go to next level.
							//if($subEle->type == 1 && $subEle->read()){
										genSubCollTo($subCollection, $level-1);
							//}
							echo '</li>';
					}
				}
				echo '</ul>';
				// display sublist
				echo '<ul class="lists">';
				if(!empty($collection->subItems)){
					foreach($collection->subItems as $subEle){
						echo '<li>';
						genOneEle($collection,$subEle);// html
						echo '</li>';
					}
					
				}
				echo '</ul>';
			}else{
				echo '<ul class="subcollections"></ul>';
				echo '<ul class="lists"></ul>';
			}
			return;
		}
	}
?>
