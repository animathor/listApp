<?php
require_once '../config/Database.php';
require_once '../models/Users.php';

	// connect to db
	$database = new Database();
	$connection = $database->connect();

	// post from sign up form
	if(isset($_POST['username'])){
		$username = trim($_POST['username']);
		$email = trim($_POST['email']);
		$password = $_POST['password'];
		$confirm_pw = $_POST['confirm'];

		// set invalid massage
		$username_msg = '';
    $email_msg = '';
    $password_msg = '';
		$valid_input = true;

		$system_msg='';

		// user validate
		if(!preg_match('/^([[:alnum:]]{1,255})$/',$username)){
			$username_msg = "Only letters and digits";
			$valid_input = false;
		}else	if(User::isRegistered($username,$connection)){
			$username_msg = "Username ".$username." is registered!";
			$valid_input = false;
		}

		// email validate
		if(filter_var($email,FILTER_VALIDATE_EMAIL)===false){
			$email_msg = "Invalid email!";
			$valid_input = false;
		}
		// password validate
		if($size = strlen($password) < 10 || $size > 20){
			$password_msg = "10 to 20 charactors!";
			$valid_input = false;
		}else if(!preg_match('/[[:alpha:]]+/',$password)){
			$password_msg = "At least one letter of the alphabet!";
			$valid_input = false;
		}else if(!preg_match('/[[:digit:]]+/',$password)){
			$password_msg = "At least one digit!";
		}else if($password != $confirm_pw){
			$password_msg = "Passwords do not match!";
			$valid_input = false;
		}
		if($valid_input === true){
		// store into db

			$new_user = new User($connection);
			$new_user->username = $username; 
			$new_user->email = $email; 
			$new_user->password =$password;
			if($new_user->create()){

				// Auto login, set user info into $_SESSION
				session_start();
				$_SESSION['user_id'] = $new_user->id;
				$_SESSION['username'] = $username;
				$_SESSION['home_collection_id'] = $new_user->home_collection_id;
				header("Location:../index.php");
				exit;
			}else{
				$system_msg = "Sorry, something gone wrong.";
			}
		}

		// invalid input or database error
		header("Location:sign_up.php?".
						"system_msg=".$system_msg."&".
						"username=".urlencode($username)."&".
						"email=".urlencode($email)."&".
						"username_msg=".$username_msg."&". 
				    "email_msg=".$email_msg."&". 
				    "password_msg=".$password_msg);

	}	
?>
