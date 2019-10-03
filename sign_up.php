<?php
	$username = '';
  $email = '';
	$system_msg = '';
	$username_msg = '';
  $email_msg = '';
  $password_msg = '';
	if(isset($_GET['username'])){
		$username = strip_tags($_GET['username']);
    $email = strip_tags($_GET['email']);
		$system_msg = strip_tags($_GET['system_msg']);
		$username_msg = strip_tags($_GET['username_msg']);
    $email_msg = strip_tags($_GET['email_msg']);
    $password_msg = strip_tags($_GET['password_msg']);
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
<?php
	if(strlen($system_msg)>0){
		echo '<h3 id="system_msg">'.$system_msg.'</h3>';
	}
?>
<form action="components/create_user.php" method="post">
	<fieldset>
		<div>
		<label for="username">username</label>
		<input type="text" name="username" value="<?php echo $username?>"/>
			<?php
				if(strlen($username_msg)>0){
					echo '<h3 id="username_msg">'.$username_msg.'</h3>';
				}
			?>
		</div>
		<div>
		<label for="email">email</label>
		<input type="text" name="email" value="<?php echo $email?>"/>
			<?php
				if(strlen($email_msg)>0){
					echo '<h3 id="email_msg">'.$email_msg.'</h3>';
				}
			?>
		</div>
		<div>
			<label for="password">password</label>
			<input type="password" name="password"/>
			<?php
				if(strlen($password_msg)>0){
					echo '<h3 id="password_msg">'.$password_msg.'</h3>';
				}
			?>
		</div>
		<div>
		<label for="confirm">confirm password</label>
		<input type="password" name="confirm"/>
		</div>
		<div>
		<input type="submit" value="sign up"/>
		</div>
	</fieldset>
</form>
<div>
	<a href="sign_in.php">Sign in</a>
</div>
</body>
