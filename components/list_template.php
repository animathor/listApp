<?php
	include_once '../config/Database.php';
	include_once '../models/Items_obj.php';
	
	function genEditForm($item,$isList){
		// delete Option for head, because the template will read a ghost list if it suicide(kill the head item).
		if($isList===true){
			$itemlink ='<a href="list_template.php?id='.$item->id.'&type='.$item->type.'">'.$item->title.'</a>';
			$deleteButt = '<a class="item-control" href="delete_item.php?item_id='.$item->id.'&item_type='.$item->type.'">&cross;</a>';
		}else{
			$deleteButt = '';
			$itemlink = '';
		}
		switch($item->type){
			case 2:
				echo '<div class="item">'.
							'<div class="item-edit">';
				echo
								$itemlink.
								'<form action="update_item.php?item_id='.$item->id.'&item_type='.$item->type.'" method="post">'.
									'<input type="text" name="title" value="'.$item->title.'" >'.
									'<textarea name="note">'.$item->note.'</textarea>'.
									'<input type="submit" value="save">'.
								'</form>'.
								'</div>';
				echo '<div class="item-control">'.$deleteButt.'</div></div>';	
				break;
			case 4:
				echo '<div class="check">'.
							'<div class="check-edit">';
				echo
								'<form action="update_item.php?item_id='.$item->id.'&item_type='.$item->type.'" method="post">';
									if($item->checked){
										echo '<input type="checkbox" name="checked" value=true checked="checked">';
									}else{
										echo '<input type="checkbox" name="checked" value=true>';
									}
									echo
									$itemlink.
									'<input type="text" name="title" value="'.$item->title.'" >'.
									'<textarea name="note">'.$item->note.'</textarea>'.
									'<input type="submit" value="save">'.
								'</form>'.
								'</div>';
				echo '<div class="check-control">'.$deleteButt.'</div></div>';		
				break;
			case 6:
				echo '<div class="task">'.
							'<div class="task-edit">';
				echo	
								'<form action="update_item.php?item_id='.$item->id.'&item_type='.$item->type.'" method="post">';
									if($item->checked){
										echo '<input type="checkbox" name="checked" value=true checked="checked">';
									}else{
										echo '<input type="checkbox" name="checked" value=true>';
									}
									echo
										$itemlink.
										'<input type="text" name="title" value="'.$item->title.'" ></br>';
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
									'<input type="submit" value="save"></div>';
					echo '</form>'.
								'</div>';
				echo '<div class="task-control">'.$deleteButt.'</div></div>';	
				break;
		}//end switch
		
	}
	
	function genSubitemTo($item, $level){
		// add item input send to create
		echo '<form class="add-new-item" action="add_new_item.php?list_id='.$item->id.'&list_type='.$item->type.'" method="post">'.
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
	
	session_start();
	
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

<!Doctype html>
<html>
	<head>
		<meta charset="utf-8"/>
		<title>list</title>
		<link href="css/list.css" type="text/css" rel="stylesheet">
	</head>
<body>
<?php
			// display list
		echo '<h1>'.$theList->title.'</h1>';

		genEditForm($theList, false);
		
		// navigator
		echo '<nav>';
		// show path
		$item_train = $theList->traceBack();//supitems
		$link_train = '<span id="nav-current-item">'.$theList->title.'</span>';
		foreach($item_train as $item){
			$link_train = '<a href=list_template.php?id='.$item->id.'&type='.$item->type.'>'.$item->title.'</a> /'.$link_train;
		}
		// link Back to collection
		if(empty($item_train)){
			$item_train[] = $theList;
		}
		$collection = end($item_train)->in_collection();
		echo '<span><a href=collection_template.php?id='.$collection->id.'>Back to '.$collection->title.'</a></span><br/>';
		echo "<span>{$link_train}</span>";
		echo '</nav>';
		
		// responce		
		if(isset($_SESSION['message'])){
			echo "<h2>".$_SESSION['message']."</h2>";
			unset($_SESSION['message']);
		}

		

		// generate subitems
		genSubitemTo($theList, 2)


?>
</body>
