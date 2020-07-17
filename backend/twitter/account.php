<?php  require_once 'templates/header.php';?>
<?php  
	if( !empty( $_POST )){
		try {
			$user_obj = new Cl_User();
			$data = $user_obj->account( $_POST );
			if($data)$success = PASSOWRD_CHANAGE_SUCCESS;
		} catch (Exception $e) {
			$error = $e->getMessage();
		} 
	}
?>

	<div class="content">
     	<div class="container">
     		<div class="col-md-8 col-sm-8 col-xs-12">
     			<?php  require_once 'templates/message.php';?>
     			<h1>My Account:</h1><br>
				<form action="<?php  echo $_SERVER['PHP_SELF']; ?>" id="account-form" method="post" class="form-horizontal myaccount" role="form">
					<div class="form-group">
						<span for="inputEmail3" class="col-sm-4 control-span">Name</span>
						<div class="col-sm-8">
							<p> <?php  echo $_SESSION['name']; ?> </p>
						</div>
					</div>
					<div class="form-group">
						<span for="inputPassword3" class="col-sm-4 control-span">Email</span>
						<div class="col-sm-8">
							<p> <?php  echo $_SESSION['email']; ?> </p>
						</div>
					</div>
					<hr>
					<div class="form-group">
						<span for="inputPassword3" class="col-sm-4 control-span">Current Password</span>
						<div class="col-sm-8">
							<input name="old_password" id="old_password" type="text">
							<span class="help-block"></span>
						</div>
					</div>
					
					<div class="form-group">
						<span for="inputPassword3" class="col-sm-4 control-span"> New Password</span>
						<div class="col-sm-8">
							<input name="password" id="password" type="text">
							<span class="help-block"></span>
						</div>
					</div>
					<div class="form-group">
						<span for="inputPassword3" class="col-sm-4 control-span"> Confirm Password</span>
						<div class="col-sm-8">
							<input name="confirm_password" id="confirm_password" type="text">
							<span class="help-block"></span>
						</div>
					</div>
					<input type="hidden" id="user_id" name="user_id" value="<?php  echo $_SESSION['user_id']; ?>">
					<input type="hidden" id="email" value="<?php  echo $_SESSION['email']; ?>" />
					
					
					<div class="form-group">
						<div class="col-sm-offset-4 col-sm-8">
							<button type="submit" class="btn btn-default">Change Password</button>
						</div>
					</div>
				</form>
		</div>
     		<?php  require_once 'templates/sidebar.php';?>
     		
     	</div>
    </div> <!-- /container -->
<script src="js/jquery.validate.min.js"></script>
<script src="js/account.js"></script>    
<?php  require_once 'templates/footer.php';?>
	

