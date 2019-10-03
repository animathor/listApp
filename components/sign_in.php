<?php
require_once 'config/Database.php';
require_once 'models/Users.php';
	session_start();
	if(isset($_SESSION['username'])){
		// do not support multiple users. Sign out first
		header("Location:../index.php");
		exit;
	}

	$username = '';
	$system_msg = '';

	if(isset($_POST['username']) && strlen($_POST['username'])>0){
		$username = $_POST['username'];
		$password = $_POST['password'];
		//connect to db
		$database = new Database();
		$connection = $database->connect();
		$user = new User($connection);
		if($user->signin($username) && $user->id !== NULL){
			// registered user, verify the password
			$pw_hash = $user->password;
			if(password_verify($password, $pw_hash)){
				// successfully sign in
				$_SESSION['user_id'] = $user->id;
				$_SESSION['username'] = $user->username;
				$_SESSION['home_collection_id'] = $user->home_collection_id;
				header("Location:../index.php");
				exit;
			}else{
				$system_msg = "Incorrect password";
			}
		}else{
		// user not found
				$system_msg = "User not found";
		}
	}

?>
<!Doctype html>
<html>
	<head>
		<meta charset="utf-8"/>
		<title>file</title>
		<link href="css/frontyard.css" type="text/css" rel="stylesheet"/>
	</head>
</html>
<body>
<form action="sign_in.php" method="post">
	<fieldset>
		<h3 id="system-msg"><?php echo $system_msg;?></h3>
		<div>
		<label>username</label>
		<input type="text" name="username" value="<?php echo $username;?>"/>
		</div>
		<div>
		<label>password</label>
		<input type="password" name="password"/>
		</div>
		<div>
		<input type="submit" value="sign in"/>
		</div>
	</fieldset>
</form>
<div>
	<a href="sign_up.php">Sign up</a>
</div>
</body>
