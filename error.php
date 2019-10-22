<?php
	include 'authorize.php';// successfully sign in, $user_id, $username and $home_collection_id are set.
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
		echo '<h2>Sorry</h2>';
		$massage = '';
		if(isset($_SESSION['message'])){
			$massage = $_SESSION['message'];
			unset($_SESSION['message']);
		}
		echo "<h3 id='message-board'>".$massage."</h3>";
?>
	</body>
</html>
