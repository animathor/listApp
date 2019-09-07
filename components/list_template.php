<?php
	include_once '../config/Database.php';
	include_once '../models/Items_obj.php';
	
	function genEditForm($item,$isList){
		//min date value of scedule and due is today
		$today = date("Y-m-d", time());
		// delete Option for head, because the template will read a ghost list if it suicide(kill the head item).
		if($isList===true){
			$itemlink ='<a href=list_template.php?id='.$item->id.'&type='.$item->type.'>'.$item->title.'</a>';
			$deleteButt = '<a href=delete_item.php?item_id='.$item->id.'&item_type='.$item->type.'>X</a>';
		}else{
			$deleteButt = '';
			$itemlink = '';
		}
		switch($item->type){
			case 2:
				echo
								$itemlink.
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
									$itemlink.
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
										$itemlink.
										'<input type="text" name="title" value="'.$item->title.'" >';
										$dueDate='';
										$dueTime='';
										if(isset($item->due)){
											list($dueDate, $dueTime) = explode(' ',$item->due);
										}
									
									if(empty($minday = $dueDate)){
										$minday = $today;// min at recent record.
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

		// display list
		echo '<h1>'.$theList->title.'</h1>';
		if(isset($_SESSION['message'])){
			echo "<h2>".$_SESSION['message']."</h2>";
			unset($_SESSION['message']);
		}
	}

	echo '<!Doctype html><html><head>'.
				'<meta charset="utf-8"/>'.
				'<title>'.$theList->title.'</title>'.
													'</head>';
	echo'<body>';

		genEditForm($theList, false);
		
		
		// show path
		$item_train = $theList->traceBack();//supitems
		$link_train = $theList->title;
		foreach($item_train as $item){
			$link_train = '<a href=list_template.php?id='.$item->id.'&type='.$item->type.'>'.$item->title.'</a> /'.$link_train;
		}
		// link Back to collection
		if(empty($item_train)){
			$item_train[] = $theList;
		}
		$collection = end($item_train)->in_collection();
		echo '<a href=collection_template.php?id='.$collection->id.'>Back to '.$collection->title.'</a><br/>';
		echo $link_train;
		
		// add item input send to create
		echo '<form action="add_new_item.php?list_id='.$theList->id.'&list_type='.$theList->type.'" method="post">'.
						'<input type="text" name="item_title" placeholder="add new"><br />'.
					'</form>';

		// generate subitems
		if($theList->readSubitems() && !empty($theList->subItems)){
			echo '<ul>';
			foreach($theList->subItems as $subItem){
				echo "<li>";
				genEditForm($subItem, true);
				echo "</li>";
			}
			echo '</ul>';
		}


?>
</body>
