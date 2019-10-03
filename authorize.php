<?php
	session_start();

	if((!isset($_SESSION['user_id'])) || (!strlen($_SESSION['user_id']) > 0)){
		header("Location:sign_in.php");
		exit;
 	}
?>
