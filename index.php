<?php
include_once 'authorize.php';// successfully sign in, session['user_id'] and session['home_collection_id'] are set.

$user_id = $_SESSION['user_id'];
$username =htmlspecialchars($_SESSION['username']);
$home_collection_id = $_SESSION['home_collection_id'];

?>
<!Doctype html>
<html>
	<head>
		<meta charset="utf-8"/>
		<title><?php echo $username.'\'s '?>listo</title>
		<link href="css/header.css" type="text/css" rel="stylesheet"/>
		<link href="css/index.css" type="text/css" rel="stylesheet"/>
	</head>
</html>
<body>
	<?php include_once 'header.php';?>
	<aside id="collection">
	</aside>
	<div id="content">
		<div id="default-functions">
			<div id="file">
				<div class="fuction-content">
					<h3>Open</h3>
					<a href="collection.php?id=<?php echo $home_collection_id; ?>">My collections</a>
				</div>
			</div>
			<div id="quick-start">
				<div class="fuction-content">
					<h3>Quick start</h3>			
							<a href="components/add_new_list.php?id=<?php echo $home_collection_id;?>&list_type=<?php echo 2;?>">items</a>
							<a href="components/add_new_list.php?id=<?php echo $home_collection_id;?>&list_type=<?php echo 4;?>">checks</a>
							<a href="components/add_new_list.php?id=<?php echo $home_collection_id;?>&list_type=<?php echo 6;?>">tasks</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</body>
