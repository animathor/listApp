	<div id="header">
			<div id = "user-edit">
				<a href=><?php echo $username;?></a>
			</div>
			<div id = "page-control">
			<?php
			if(!preg_match('/index/',$_SERVER['PHP_SELF'])){
				echo '<a href ="../index.php">home</a>';
				}
			?>
			<a href="sign_out.php">sign_out</a>
			</div>
	</div>
