<?php
	$username = '';
  $email = '';
	$system_msg = '';
	$username_msg = '';
  $email_msg = '';
  $password_msg = '';
  $confirm_msg = '';
	if(isset($_GET['username'])){
		$username = strip_tags($_GET['username']);
    $email = strip_tags($_GET['email']);
		$system_msg = strip_tags($_GET['system_msg']);
		$username_msg = strip_tags($_GET['username_msg']);
    $email_msg = strip_tags($_GET['email_msg']);
    $password_msg = strip_tags($_GET['password_msg']);
    $confirm_msg =strip_tags($_GET['confirm_msg']);
	}

?>
<!Doctype html>
<html>
	<head>
		<meta charset="utf-8"/>
		<title>Sign up</title>
		<link href="css/frontyard.css" type="text/css" rel="stylesheet"/>
	</head>

<body>
	<h3 id="system_msg"><?php echo $system_msg;?></h3>
<form action="components/create_user.php" method="post">
	<fieldset>
		<div>
		<label for="username">username</label></br>
		<input class="text-input" type="text" id="username" name="username" placeholder="6 to 255 charactors of aplabet and numbers" value="<?php echo $username?>" required >
					<h3 class="message-board" id="username_msg"><?php echo $username_msg;?></h3>
		</div>
		<div>
		<label for="email">email</label></br>
		<input class="text-input" type="email" id="email" name="email" value="<?php echo $email?>"required>
					<h3 id="email_msg"><?php echo $email_msg; ?></h3>
		</div>
		<div>
			<label for="password">password</label></br>
			<input class="text-input" type="password" id="password" name="password" placeholder="10 to 20 charactors of aplabet and numbers" required>
					<h3 id="password_msg"><?php echo $password_msg; ?></h3>
		</div>
		<div>
		<label for="confirm">confirm password</label></br>
		<input class="text-input" type="password" id="confirm" name="confirm" required >
					<h3 id="confirm_msg"><?php echo $confirm_msg; ?></h3>
		</div>
		<div>
		<input type="submit" name="sign_up" value="Sign up">
		</div>
	</fieldset>
</form>
<div>
	<a href="sign_in.php">Sign in</a>
</div>
<script src="script/utilities.js"></script>
<script src="script/sign_up_validate.js"></script>
</body>
</html>
