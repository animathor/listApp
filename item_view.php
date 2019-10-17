<?php
function genEditForm($item,$isItem){
		$item_title = $item->title;
		$item_note = $item->note;
	
		// delete Option for head, because the template will read a ghost list if it suicide(kill the head item).
		if($isItem===true){
			$itemlink ='<a class="title-link" href="list_template.php?id='.$item->id.'&type='.$item->type.'">'.$item_title.'</a>';
			$deleteButt = '<a class="delete-button" href="components/delete_item.php?item_id='.$item->id.'&item_type='.$item->type.'">&cross;</a>';
			$editButt = '<div class="edit-button"></div>';//'<img class="edit-button" src="img/edit_blue.png">';
			$headId='';
		}else{
			$deleteButt = '';
			$itemlink = '';
			$editButt = '<img id="head-edit-button" class="edit-button" src="img/edit.png">';
			$headId = 'id="head"';
		}
		switch($item->type){
			case ITEM_TYPE:
				echo '<div '.$headId.' class="item type-item">'.
							'<div class="item-edit">';
				echo '<div class="item-control">'.$editButt.$deleteButt.'</div>';
				echo
								$itemlink.
								'<form action="components/update_item.php?item_id='.$item->id.'&item_type='.$item->type.'" method="post" data-item_id='.$item->id.' data-item_type='.$item->type.'>'.
									'<input class="edit-title" type="text" name="title" value="'.$item_title.'" >'.
									'<div class="edit-panel">'.
									'<textarea name="note">'.$item_note.'</textarea>'.
									'<input type="submit" value="save">'.
									'</div>'.
								'</form>'.
								'</div></div>';
					
				break;
			case CHECK_TYPE:
				// is checked?
				if($item->checked){
					// checked is true, so the button sends false
					$checkBox = '<button type="submit" class="checkbox checked" name="checked" value="false">&checkmark;</button>';
					// classname for item which is checked
					$ischecked = ' checked';
				}else{
					$checkBox = '<button type="submit" class="checkbox" name="checked" value="true">&checkmark;</button>';
					$ischecked = '';
				}
				echo '<div '.$headId.' class="item type-check'.$ischecked.'">'.
							'<div  class="check-edit">';
				echo '<div class="check-control">'.$editButt.$deleteButt.'</div>';
				echo	
								'<form action="components/check_item.php?item_id='.$item->id.'&item_type='.$item->type.'" method="post">';
					echo $checkBox;
				echo '</form>';
				echo
								'<form  action="components/update_item.php?item_id='.$item->id.'&item_type='.$item->type.'" method="post"  data-item_id='.$item->id.' data-item_type='.$item->type.'>';
									
									echo
									$itemlink.
									'<input class="edit-title" type="text" name="title" value="'.$item_title.'" >'.
									'<div class="edit-panel">'.
									'<textarea name="note">'.$item_note.'</textarea>'.
									'<input type="submit" value="save">'.
									'</div>'.
								'</form>'.
								'</div></div>';
		
				break;
			case TASK_TYPE:
			// is checked?
				if($item->checked){
					// checked is true, so the button sends false
					$checkBox = '<button type="submit" class="checkbox checked" name="checked" value="false">&checkmark;</button>';
					// classname for item which is checked
					$ischecked = ' checked';
				}else{
					$checkBox = '<button type="submit" class="checkbox" name="checked" value="true">&checkmark;</button>';
					$ischecked = '';
				}
				echo '<div '.$headId.' class="item type-task'.$ischecked.'">'.
							'<div  class="task-edit">'.
								'<div class="task-control">'.$editButt.$deleteButt.'</div>';
				echo	
								'<form action="components/check_item.php?item_id='.$item->id.'&item_type='.$item->type.'" method="post">';
									echo $checkBox;
				echo 		'</form>';
				echo	
								'<form  action="components/update_item.php?item_id='.$item->id.'&item_type='.$item->type.'" method="post" data-item_id='.$item->id.' data-item_type='.$item->type.'>';
								echo
										$itemlink.
										'<input class="edit-title" type="text" name="title" value="'.$item_title.'" ></br>';
									echo'<div class="edit-panel">';
									//	schedule
									$scheduleDate='';
									$scheduleTime='';
									if(isset($item->schedule)){
										list($scheduleDate, $scheduleTime) = explode(' ', $item->schedule);
									}
									
									echo
									'<div>'.
									'<label>schedule</label>'.
									'<input type="date" name="scheduleDate" value = "'.$scheduleDate.
										'"pattern="[0-9]{4}-[0-9]{2}-[0-9]{2}" step="1">'.
									'<input type="time" name="scheduleTime" value = "'.$scheduleTime.
									'" pattern="[0-9]{2}:[0-9]{2}:[0-9]{2}" step="1"><br/>'.
									'</div>';
									// due
									$dueDate='';
									$dueTime='';
										if(isset($item->due)){
											list($dueDate, $dueTime) = explode(' ',$item->due);
										}
									
									echo
									'<div>'.
									'<label>due</label>'.
									'<input type="date" name="dueDate" value = "'.$dueDate.
										'"pattern="[0-9]{4}-[0-9]{2}-[0-9]{2}" step="1">'.
									'<input type="time" name="dueTime" value = "'.$dueTime.
										'" pattern="[0-9]{2}:[0-9]{2}:[0-9]{2}" step="1">'.
									'</div>';
					echo		'<div><textarea name="note">'.$item_note.'</textarea>'.
									'<input type="submit" value="save">'.
									'</div>'.
									'</div>';
					echo '</form></div>'.
								'</div>';
			
				break;
		}//end switch
		
	}
	function genAddNew($item){
	echo '<form class="add-new-item" action="components/add_new_item.php?list_id='.$item->id.'&list_type='.$item->type.'" method="post">'.
						'<input type="text" name="item_title" placeholder="add new item"><br />'.
					'</form>';
	}
	
	function genSubitemTo($item, $level){
		
		if($level==0){
			return;
		}else{
			 // add item input send to create
			genAddNew($item);
			echo '<ul>';
			if($item->readSubitems() && !empty($item->subItems)){
			
				foreach($item->subItems as $subItem){
					echo "<li>";
					genEditForm($subItem, true);
					genSubitemTo($subItem, $level-1);
					echo "</li>";
				}
			}
			echo '</ul>';
		}
		return;
	}
?>
