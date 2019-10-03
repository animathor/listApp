<?php
	include_once 'config/Database.php';
	include_once 'models/Items_obj.php';
	include_once 'authorize.php';// successfully sign in, session['user_id'] and session['home_collection_id'] are set.
	
	function genEditForm($item,$isItem){
		// delete Option for head, because the template will read a ghost list if it suicide(kill the head item).
		if($isItem===true){
			$itemlink ='<a class="title-link" href="list_template.php?id='.$item->id.'&type='.$item->type.'">'.$item->title.'</a>';
			$deleteButt = '<a class="delete-button" href="components/delete_item.php?item_id='.$item->id.'&item_type='.$item->type.'">&cross;</a>';
			$editButt = '<div class="edit-button"></div>';//'<img class="edit-button" src="img/edit_blue.png">';
			$headEditId='';
		}else{
			$deleteButt = '';
			$itemlink = '';
			$editButt = '<img id="head-edit-button" class="edit-button" src="img/edit.png">';
			$headEditId = 'id="head-edit"';
		}
		switch($item->type){
			case 2:
				echo '<div class="item type-item">'.
							'<div class="item-edit">';
				echo '<div class="item-control">'.$editButt.$deleteButt.'</div>';
				echo
								$itemlink.
								'<form '.$headEditId.' action="components/update_item.php?item_id='.$item->id.'&item_type='.$item->type.'" method="post">'.
									'<input class="edit-title" type="text" name="title" value="'.$item->title.'" >'.
									'<div class="edit-panel">'.
									'<textarea name="note">'.$item->note.'</textarea>'.
									'<input type="submit" value="save">'.
									'</div>'.
								'</form>'.
								'</div></div>';
					
				break;
			case 4:
				echo '<div class="item type-check">'.
							'<div  class="check-edit">';
				echo '<div class="check-control">'.$editButt.$deleteButt.'</div>';							
				echo
								'<form '.$headEditId.' action="components/update_item.php?item_id='.$item->id.'&item_type='.$item->type.'" method="post">';
									if($item->checked){
										echo '<input type="checkbox" name="checked" value=true checked="checked">';
									}else{
										echo '<input type="checkbox" name="checked" value=true>';
									}
									echo
									$itemlink.
									'<input class="edit-title" type="text" name="title" value="'.$item->title.'" >'.
									'<div class="edit-panel">'.
									'<textarea name="note">'.$item->note.'</textarea>'.
									'<input type="submit" value="save">'.
									'</div>'.
								'</form>'.
								'</div></div>';
		
				break;
			case 6:
				echo '<div class="item type-task">';
				echo '<div  class="task-edit">'.
								'<div class="task-control">'.$editButt.$deleteButt.'</div>';
				echo	
								'<form '.$headEditId.' action="components/update_item.php?item_id='.$item->id.'&item_type='.$item->type.'" method="post">';
									if($item->checked){
										echo '<input type="checkbox" name="checked" value=true checked="checked">';
									}else{
										echo '<input type="checkbox" name="checked" value=true>';
									}
									echo
										$itemlink.
										'<input class="edit-title" type="text" name="title" value="'.$item->title.'" ></br>';
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
					echo		'<div><textarea name="note">'.$item->note.'</textarea>'.
									'<input type="submit" value="save">'.
									'</div>'.
									'</div>';
					echo '</form></div>'.
								'</div>';
			
				break;
		}//end switch
		
	}
	
	function genSubitemTo($item, $level){
		// add item input send to create
		echo '<form class="add-new-item" action="components/add_new_item.php?list_id='.$item->id.'&list_type='.$item->type.'" method="post">'.
						'<input type="text" name="item_title" placeholder="add new"><br />'.
					'</form>';
		if($level==0){
			return;
		}else if($item->readSubitems() && !empty($item->subItems)){
			echo '<ul>';
			foreach($item->subItems as $subItem){
				echo "<li>";
				$today = date("Y-m-d", time());
				genEditForm($subItem, true, $today);
				genSubitemTo($subItem, $level-1);
				echo "</li>";
			}
			echo '</ul>';
		}
		return;
	}
	
	// Connect to database	
	$database = new Database();
	$connection = $database->connect();
	
	
	// read list
		if(isset($_GET['id']) && isset($_GET['type'])){
			$id = $_GET['id'];
			$type = $_GET['type'];
			$id_n_type = ['id'=>$id, 'type'=>$type];

			
		}else	if(isset($_SESSION['current_list'])){
			$id_n_type = $_SESSION['current_list'];
		}else{
				header("Location:collection_template.php");
		}
			// prepare model
		$itemx = new ItemX($connection, $id_n_type);
		$theList = $itemx->container;// fetch the real obj

		if($theList->read()){
			// note down the current list
				$_SESSION['current_list'] = $id_n_type;
		}else{
			header("Location:collection_template.php");
		}

?>

<?php


$user_id = $_SESSION['user_id'];
$username =$_SESSION['username'];
$home_collection_id = $_SESSION['home_collection_id'];
//$username = 'Alan';
//$home_collection_id =1;
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
		echo '<h2>'.$theList->title.'</h2>';

		genEditForm($theList, false);
		
		// navigator
		echo '<nav>';
		// show path
		$item_train = $theList->traceBack();//supitems
		$link_train = '<li id="nav-current-item">'.$theList->title.'</li>';
		foreach($item_train as $item){
			$link_train = '<li><a href=list_template.php?id='.$item->id.'&type='.$item->type.'>'.$item->title.'</a> /</li>'.$link_train;
		}
		// link Back to collection
		if(empty($item_train)){
			$item_train[] = $theList;
		}
		$collection = end($item_train)->in_collection();
		echo '<span><a href=collection_template.php?id='.$collection->id.'>Back to '.$collection->title.'</a></span><br/>';
		echo "<ul>{$link_train}</ul>";
		echo '</nav>';
		
		// responce		
		if(isset($_SESSION['message'])){
			echo "<h2>".$_SESSION['message']."</h2>";
			unset($_SESSION['message']);
		}

		

		// generate subitems
		genSubitemTo($theList, 2)


?>
<script src="script/hide.js"></script>
<script src="script/list_hide.js"></script>
</body>
