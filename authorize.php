<?php
	session_start();

	if((!isset($_SESSION['user_id'])) || (!strlen($_SESSION['user_id']) > 0)){
		if(isset($_POST['ajax'])){
			// session time out
			http_response_code(500);
			header("Content-type:application/json");			
			echo json_encode(["success"=>false, "timeout"=>true]);
			exit;
		}else{
			if(strpos($_SERVER['PHP_SELF'],'components')){
				$signIn ="../sign_in.php";
			}else{
				$signIn ="sign_in.php";
			}
			header("Location:".$signIn);
			exit;
		}
 	}else{
 	// set user info
 		$user_id = $_SESSION['user_id'];
		$username =$_SESSION['username'];
		$home_collection_id = $_SESSION['home_collection_id'];
 	}

?>
