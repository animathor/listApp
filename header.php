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
