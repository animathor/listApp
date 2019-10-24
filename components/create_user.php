<?php
/*
** Read list and Display it.
** // 1) Get form data
** // 2) Connect to Database 
** // 3) Validate user, email, passwords
** //    -username uniqueness
** // 4) Valid: store data and sign in,  Invalid: return
*/

require '../config/Database.php';
require '../models/Users.php';


	// 1) Get form data
	//  post from sign up form
	if(isset($_POST['username'])){
		$username = trim($_POST['username']);
		$email = trim($_POST['email']);
		$password = $_POST['password'];
		$confirm_pw = $_POST['confirm'];

		try{
			// 2) Connect to Database 
			$database = new Database();
			$connection = $database->connect();
		}catch(dbConnectExceptioin $dbe){
			$system_msg = $e->getMessage();
			header("Location:sign_in.php?system_msg=".$system_msg);
		}
		
		// 3) Validate user, email, passwords
		// set massage for invalid input
		$username_msg = '';
    $email_msg = '';
    $password_msg = '';
		$valid_input = true;// invalid input Flag

		$system_msg='';

		// user validate
		try{
			if(!preg_match('/^([[:alnum:]]{6,255})$/',$username)){
				$username_msg = "6 to 255 characters of letters and digits";
				$valid_input = false;
			}else	if(User::isRegistered($username,$connection)){
				$username_msg = "Username ".$username." is registered!";
				$valid_input = false;
			}
		}catch(Exception $e){
			$system_msg = $e->getMessage();			
			header("Location:sign_in.php?system_msg=".$system_msg);			
		}
		
		// email validate
		if(filter_var($email,FILTER_VALIDATE_EMAIL)===false){
			$email_msg = "Please enter a valid email!";
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
			$valid_input = false;
		}
		// confirm password
		if($password !== $confirm_pw){
			$confirm_msg = "Passwords do not match!";	
			$valid_input = false;
		}
		
		// 4) Valid: store data and sign in,  Invalid: return
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

		// invalid input 
		header("Location:../sign_up.php?".
						"system_msg=".$system_msg."&".
						"username=".urlencode($username)."&".
						"email=".urlencode($email)."&".
						"username_msg=".$username_msg."&". 
				    "email_msg=".$email_msg."&". 
				    "password_msg=".$password_msg."&".
				    "confirm_msg=".$confirm_msg);
		exit;
	}// end if $_POST is set
	header("Location:../sign_up.php");
?>
