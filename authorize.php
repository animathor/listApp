<?php
	session_start();

	if((!isset($_SESSION['user_id'])) || (!strlen($_SESSION['user_id']) > 0)){
		header("Location:sign_in.php");
		exit;
 	}else{
 	// set user info
 		$user_id = $_SESSION['user_id'];
		$username =$_SESSION['username'];
		$home_collection_id = $_SESSION['home_collection_id'];
 	}
?>
