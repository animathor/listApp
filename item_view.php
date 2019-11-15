<?php
	function div($content = '', $attribute = ''){
		return '<div '.$attribute.'>'.$content.'</div>';
	}
	function dateTimeSplit($dateTimeName, $dateTimeValue){
		$date = $time ='';
		if(isset($dateTimeValue)){
			list($date, $time) = explode(' ', $dateTimeValue);
		}
		return '<div>'.
								'<label>'.$dateTimeName.'</label>'.
							 	'<input type="date" name="'.$dateTimeName.'" value = "'.$date.
								'"pattern="[0-9]{4}-[0-9]{2}-[0-9]{2}" step="1">'.
							 	'<input type="time" name="'.$dateTimeName.'" value = "'.$time.
								'" pattern="[0-9]{2}:[0-9]{2}:[0-9]{2}" step="1"><br/>'.
						'</div>';
	}
	
	function displayCheckBox($item){
		if($item->checked){
			// checked is true, so the button sends false
			$checkBox = '<button type="submit" class="checkbox checked" name="checked" value="false">&checkmark;</button>';
		}else{
			$checkBox = '<button type="submit" class="checkbox" name="checked" value="true">&checkmark;</button>';
		}
		echo '<form action="components/check_item.php?item_id='.$item->id.'&item_type='.$item->type.'" method="post">'.
						$checkBox.
					'</form>';
	}
	function displayEditForm($item, $content){
		echo '<form class="edit-form" action="components/update_item.php?item_id='.$item->id.'&item_type='.$item->type.'" method="post" data-item_id='.$item->id.' data-item_type='.$item->type.'>'.
			$content.
			'</form>';
	}
	
	function genOneItem($item,$isItem){
		$item_title = $item->title;
		$item_note = $item->note;
		if($isItem===true){
			$headId='';// no attribute id = head
			$itemlink ='<a class="title-link" href="list_template.php?id='.$item->id.'&type='.$item->type.'">'.$item_title.'</a>';
			$deleteButt = '<a class="delete-button" href="components/delete_item.php?item_id='.$item->id.'&item_type='.$item->type.'">&cross;</a>';
			$editButt = div('','class="edit-button"');
			$dragHandle = div('','class="drag-handle"');
			
		}else{
			$deleteButt = '';// head can't be deleted
			$itemlink = '';// head don't need link
			$editButt = '<img id="head-edit-button" class="edit-button" src="img/edit.png">';
			$dragHandle = '';// head don't need to be sortable
			$headId = 'id="head"';
		}
		$control = div($dragHandle.$editButt.$deleteButt, 'class = control');
		//edit form elements
		$input_title = '<input class="edit-title" type="text" name="title" value="'.$item_title.'" maxlength="255">';
		$input_note = '<textarea name="note">'.$item_note.'</textarea>';
		$save_button = '<input type="submit" name="save"  value="save">';

		switch($item->type){
			case ITEM_TYPE:
				echo '<div '.$headId.' class="item type-item">';
				echo   $control;
							displayEditForm($item,
									$itemlink.
									$input_title.
									div($input_note.$save_button,'class="edit-panel"')
							);
				echo '</div>';
				break;
			case CHECK_TYPE:
					// classname for item which is checked
					$ischecked = ($item->checked) ? ' checked' : '';
				echo '<div '.$headId.' class="item type-check'.$ischecked.'">';
				echo 		$control;
								displayCheckBox($item);
								displayEditForm($item,
									$itemlink.
									$input_title.
									div($input_note.$save_button,'class="edit-panel"')
								);
				echo '</div>';
				break;
			case TASK_TYPE:
				$ischecked = ($item->checked) ? ' checked' : '';
				$input_schedule = dateTimeSplit('schedule', $item->schedule);
				$input_due = dateTimeSplit('due', $item->due);
				echo '<div '.$headId.' class="item type-task'.$ischecked.'">';
				echo		$control;
								displayCheckBox($item);
								displayEditForm($item,
									$itemlink.
									$input_title.
									div(
										$input_schedule.
										$input_due.
										div($input_note.$save_button), 'class="edit-panel"')
								);
					echo '</div>';
				break;
		}//end switch
		
	}

	function genAddNew($item){
	echo '<form class="add-new-item" action="components/add_new_item.php?list_id='.$item->id.'&list_type='.$item->type.'" method="post">'.
						'<input type="text" name="item_title" placeholder="add new item" ><br />'.
					'</form>';
	}
	
	function genSubitemTo($item, $level){
		
		// the number of levels to show
		if($level == 0){
			return;
		}else{
			// add item input send to create
			genAddNew($item);
			echo '<ul class="subitems" data-id="'.$item->id.'" data-type="'.$item->type.'">';
			if($item->readSubitems() && !empty($item->subItems)){
			
				foreach($item->subItems as $subItem){
					// may has next level items, load by ajax
					if($level == 1){
						echo '<li id="item_'.$subItem->id.'" class="load-more" data-id="'.$subItem->id.'" data-type="'.$subItem->type.'">';
					}else{
						echo '<li id="item_'.$subItem->id.'" data-id="'.$subItem->id.'" data-type="'.$subItem->type.'">';
					}
					genOneItem($subItem, true);
					genSubitemTo($subItem, $level-1);
					echo "</li>";
				}
			}
			echo '</ul>';
		}
		return;
	}
?>
