	<div id="header">
			<div id = "user-edit">
				<p>Hi, <?php echo $username;?></p>
			</div>
			<div id = "page-control">
			<?php
			
			if(strpos($_SERVER['PHP_SELF'],'index') === false){
				echo '<a href ="index.php">home</a>';
			}
			echo '<a href="sign_out.php">sign out</a>';
			?>
			</div>
	</div>
	<div id="sign_in_again" class=" hide">
		<form action="sign_in.php" method="post">                               	
    	<fieldset>
    		<h3 id="system-msg">Session timeout.</br>Please sign in to continue.</h3>
    		<div>
    		<label>username</label></br>
    		<input class="text-input" type="text" name="username" value="<?php echo $username; ?>"disabled/>
    		</div>
    		<div>
    		<label>password</label></br>
    		<input class="text-input" type="password" name="password"/>
    		</div>
    		<div>
    		<button id="quit-app" type="button">Quit</button>
    		<input type="submit" name="sign_in" value="Sign in"/>
    		
    		</div>
    	</fieldset>
  	</form>
  </div>
