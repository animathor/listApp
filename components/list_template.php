<?php
	include_once '../config/Database.php';
	include_once '../models/Items_obj.php';
	
	function genEditForm($item,$deleteON){
		//min date value of scedule and due is today
		$today = date("Y-m-d", time());
		// delete Option for head, because the template will read a ghost list if it suicide(kill the head item).
		if($deleteON===true){
			$deleteButt = '<a href=delete_item.php?item_id='.$item->id.'&item_type='.$item->type.'>X</a>';
		}else{
			$deleteButt = '';
		}
		switch($item->type){
			case 2:
				echo
								'<form action="update_item.php?item_id='.$item->id.'&item_type='.$item->type.'" method="post">'.
									'<input type="text" name="title" value="'.$item->title.'" >'.
									'<textarea name="note">'.$item->note.'</textarea>'.
									'<input type="submit" value="save">'.
								'</form>'.$deleteButt;
								
				break;
			case 4:
				echo
								'<form action="update_item.php?item_id='.$item->id.'&item_type='.$item->type.'" method="post">';
									if($item->checked){
										echo '<input type="checkbox" name="checked" value=true checked="checked">';
									}else{
										echo '<input type="checkbox" name="checked" value=true>';
									}
									echo
									'<input type="text" name="title" value="'.$item->title.'" >'.
									'<textarea name="note">'.$item->note.'</textarea>'.
									'<input type="submit" value="save">'.
								'</form>'.$deleteButt;
				break;
			case 6:
				echo
								'<form action="update_item.php?item_id='.$item->id.'&item_type='.$item->type.'" method="post">';
									if($item->checked){
										echo '<input type="checkbox" name="checked" value=true checked="checked">';
									}else{
										echo '<input type="checkbox" name="checked" value=true>';
									}
									echo			
										'<input type="text" name="title" value="'.$item->title.'" >';
										$dueDate='';
										$dueTime='';
										if(isset($item->due)){
											list($dueDate, $dueTime) = explode(' ',$item->due);
										}
									echo
									'<input type="date" name="dueDate" value = "'.$dueDate.
										'"pattern="[0-9]{4}-[0-9]{2}-[0-9]{2}" min="'.$today.'" step="1">'.
									'<input type="time" name="dueTime" value = "'.$dueTime.
										'" pattern="[0-9]{2}:[0-9]{2}:[0-9]{2}" min="'.$today.'" step="1">';
									$scheduleDate='';
									$scheduleTime='';
									if(isset($item->schedule)){
										list($scheduleDate, $scheduleTime) = explode(' ', $item->schedule);
									}
									
									echo
									'<input type="date" name="scheduleDate" value = "'.$scheduleDate.
										'"pattern="[0-9]{4}-[0-9]{2}-[0-9]{2}" min="'.$today.'" step="1">'.
									'<input type="time" name="scheduleTime" value = "'.$scheduleTime.
									'" pattern="[0-9]{2}:[0-9]{2}:[0-9]{2}" min="'.$today.'" step="1"><br/>'.
									'<textarea name="note">'.$item->note.'</textarea>'.
									'<input type="submit" value="save">'.
								'</form>'.$deleteButt;
				break;
		}//end switch
	}
?>
<!--list template-->
<!Doctype html>
<html>
	<head>
		<meta charset="utf-8"/>
		<title>list</title>
	</head>
</html>
<body>
<!--create list--> 	
<?php
	// Connect to database	
	$database = new Database();
	$connection = $database->connect();
	
	session_start();
	
	// read list		
		if(isset($_GET['id']) && isset($_GET['type'])){
			$id = $_GET['id'];
			$type = $_GET['type'];
			$id_n_type = ['id'=>$id, 'type'=>$type];
			
		}else	if(isset($_SESSION['current_list'])){
			$id_n_type = $_SESSION['current_list'];
		}
		
		// prepare model
		$itemx = new ItemX($connection, $id_n_type);
		
	if($itemx->read()){
		// note down the current list
		$_SESSION['current_list'] = $id_n_type;
		
		$theList = $itemx->container;// fetch the real obj
		
		// display list
		echo '<h1>'.$theList->title.'</h1>';
		genEditForm($thelist, false);
		// add item input send to create
		echo '<form action="add_new_item.php?list_id='.$theList->id.'&item_type='.$theList->type.'" method="post">'.
						'<input type="text" name="item_title" placeholder="add new item"><br />'.
					'</form>';
		if(isset($_SESSION['message'])){
			echo "<h2>".$_SESSION['message']."</h2>";
			unset($_SESSION['message']);
		}
		// generate subitems
		if($item->readSubitems()){
			echo '<ul>';
			foreach($item->subItems as $subItem){
				echo "<li>";
				genEditForm($subItem, true);
				echo "</li>";
			}
			echo '</ul>';
		}
	}


?>
</body>
