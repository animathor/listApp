<?php
	session_start();
	unset($_SESSION['user_id']);
	unset($_SESSION['username']);
	unset($_SESSION['home_collection_id']);
	header("Location:sign_in.php");
?>
