<?php
/*	
** source: https://www.php.net/manual/en/function.checkdate.php#113205
**	note by 'glavic at gmail dot com'
*/
function validateDate($date, $format = 'Y-m-d H:i:s')
{
  $d = DateTime::createFromFormat($format, $date);
  return $d && $d->format($format) == $date;
}

?>
<?php
/*
** update item.
** // 1) Check authorization 
** // 2) Ajax?
** // 3) Get item id and type
** // 4) Connect to Database and prepare model
** // 5) Prepare and validate date values
** // 6) Update item
** // 7) Respond
*/
	include_once '../config/app_config.php';
	include_once '../config/Database.php';
	include_once '../models/ItemX.php';
	
	// 1) Check authorization 
	include '../authorize.php';// successfully sign in, $user_id, $username and $home_collection_id are set.
	// 2) Ajax?
	$ajax = isset($_POST['ajax']);
	
	try{
		 // 3) Get item id and type
		if(isset($_GET['item_id']) && preg_match('/^[0-9]+$/',$_GET['item_id']) && isset($_GET['item_type']) && preg_match(SUPPORT_TYPES_REG,$_GET['item_type'])){
			$item_id= $_GET['item_id'];
			$item_type = $_GET['item_type'];
			$id_type_arr = ['id'=>$item_id, 'type'=>$item_type];
		}else{
			throw new Exception();
		}
		
		// 4) Connect to Database and prepare model
		$database = new Database();
		$connection = $database->connect();
		//use itemX
		$item = new ItemX($connection,$id_type_arr);
		
		//bind data
			// 5) Prepare and validate date values
			//due prepare
			$today = date("Y-m-d", time());
			if(!empty($_POST['dueTime']) && !empty($_POST['dueDate'])){
				$due_value = $_POST['dueDate'].' '.$_POST['dueTime'];
				}elseif(!empty($_POST['dueDate'])){
					$due_value = $_POST['dueDate'].' 23:59:59';//default is the end of the day if the date is set
				}elseif(!empty($_POST['dueTime'])){
					$due_value = $today.' '.$_POST['dueTime'];//default is today if the time is set
				}else{
					$due_value=null;
				}
				// validate date format
				if($due_value != null && validateDate($due_value)){
					throw new Exception("Please enter due date in 'YYYY-MM-DD' and time in 'HH-ii-ss'");
				}

			//schedule prepare
			if(!empty($_POST['scheduleTime']) && !empty($_POST['scheduleDate'])){
				$schedule_value = $_POST['scheduleDate'].' '.$_POST['scheduleTime'];
				}elseif(!empty($_POST['scheduleDate'])){
					$schedule_value = $_POST['scheduleDate'].' 00:00:00';//default is the begin of the day if the date is set
				}elseif(!empty($_POST['scheduleTime'])){
					$schedule_value = $today.' '.$_POST['scheduleTime'];//default is today if the time is set
				}else{
					$schedule_value=null;
				}
				// validate date format
				if($schedule_value != null && validateDate($schedule_value)){
					throw new Exception("Please enter schedule date in 'YYYY-MM-DD' and time in 'HH-ii-ss'");
				}

		if(isset($_POST['title'])){
			if(strlen($_POST['title'])>255){
				throw new Exception('Title should be at most 255 charactors');
			}
			$title = empty($_POST['title'])? 'blank' : htmlspecialchars($_POST['title']);
		}
		$note = htmlspecialchars($_POST['note']);
		switch($item_type){
			case ITEM_TYPE:
				$item->setData('title',$title);
				$item->setData('note',$note);
				break;
			case CHECK_TYPE:
				$item->setData('title',$title);
				$item->setData('note',$note);
				break;
			case TASK_TYPE:
				$item->setData('title',$title);
				$item->setData('note',$note);	
				$item->setData('schedule',$schedule_value);
				$item->setData('due',$due_value);
				break;
		}
	}catch(dbConnectException $dbe){
		if($ajax){
				header("Content-type:application/json");
				echo json_encode(["success"=>false,
														"message"=>$e->getMessage()]);
				http_response_code(500);
				exit;
		}else{
			$_SESSION['meassage'] = $e->getMessage();
			header("Location:../list.php");
		}
	}catch(Exception $e){
		if($ajax){
				http_response_code(400);
				header("Content-type:application/json");
				echo json_encode(["success"=>false,
														"message"=>$e->getMessage()]);
				exit;
		}else{
			$_SESSION['meassage'] = $e->getMessage();
			header("Location:../list.php");
		}
	}
		// 6) Update item
		$result = $item->update();
		// 7) Respond
		if($ajax){
		// request by ajax
			if($result){
				header("Content-type:application/json");
				echo json_encode(["success"=>true,
														"title"=>$title,
														"note"=>$note]);
			}else{
				header("Content-type:application/json");
				echo json_encode(["success"=>false,
														"message"=>"item is not updated"]);
			}
		}else{
			if($result){
				$_SESSION['message'] = 'item'.$item_id.'is updated';
			}else{
				$_SESSION['message'] = 'item'.$item_id.' is not updated';
			}
			header("Location:../list.php");
		}
?>
