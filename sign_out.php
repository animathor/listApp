<?php
	session_start();
	session_unset();
	header("Location:sign_in.php");
?>
