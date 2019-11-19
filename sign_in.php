<?php
/*
** Sign in
** // 1) Get username and password
** //			username: not empty
** // 2) Connect to Database 
** //		  successfully?
** // 3) Find user's info
** //		  successfully?
** // 4) Verify password
** // 5) Display: sign in form, link to sign up
*/
require_once 'config/Database.php';
require_once 'models/Users.php';
	session_start();
	$ajax = isset($_POST['ajax']);
	
	if(!$ajax && isset($_SESSION['username'])){
		// do not support multiple users. Sign out first
		header("Location:index.php");
		exit;
	}
	// 1) Get username and password
	$username = '';
	$system_msg = '';

	if(isset($_POST['username']) && strlen($_POST['username'])>0){
		$username = $_POST['username'];
		$password = $_POST['password'];

		try{
			// 2) Connect to Database
			$database = new Database();
			$connection = $database->connect();
		

			// 3) Find user's info
			$user = new User($connection);
			if(!$user->signin($username)){
				throw new Exception("Faided to sign in.");
			}
			if($user->id === NULL){
				throw new Exception("User not found");
			}
				
			// 4) Verify password
			// registered user, verify the password
			$pw_hash = $user->password;
			if(password_verify($password, $pw_hash)){
						// successfully sign in
						$_SESSION['user_id'] = $user->id;
						$_SESSION['username'] = $user->username;
						$_SESSION['home_collection_id'] = $user->home_collection_id;
						if($ajax){
							header("Content-type:application/json");			
							echo json_encode(["success"=>true]);
							exit;
						}else{
							header("Location:index.php");
							exit;
						}
			}else{
				throw new Exception("Incorrect password");
			}
		}catch(dbConnectException $dbe){
			if($ajax){
				http_response_code(500);
				exit;
			}else{
				$_SESSION['message'] = $dbe->getMessage();
				header("Loction:error.php");
			}
		}catch(Exception $e){
			if($ajax){
				header("Content-type:application/json");			
				echo json_encode(["success"=>false, "message"=>$e->getMessage()]);
				exit;
			}else{
				$system_msg = $e->getMessage();
			}
		}
	}
	
// 5) Display: sign in form, link to sign up
?>
<!Doctype html>
<html>
	<head>
		<meta charset="utf-8"/>
		<title>Sign in</title>
		<link href="css/frontyard.css" type="text/css" rel="stylesheet"/>
	</head>
	<body>
		<form action="sign_in.php" method="post">                               	
    	<fieldset>
    		<h3 id="system-msg"><?php echo $system_msg;?></h3>
    		<div>
    		<label>username</label></br>
    		<input class="text-input" type="text" name="username" value="<?php echo $username;?>"/>
    		</div>
    		<div>
    		<label>password</label></br>
    		<input class="text-input" type="password" name="password"/>
    		</div>
    		<div>
    		<input type="submit" name="sign_in" value="Sign in"/>
    		</div>
    	</fieldset>
    </form>
	  <div>
  		<a href="sign_up.php">Sign up</a>
 	 </div>
  </body>
</html>
