<?php
use Thunderhawk\API\Engine;
session_start ();
$enginePath = __DIR__ . '/../../core/API/Engine.php';
$installed = false;
$version = null;
$engine = null;
$user = false;
if (file_exists ( $enginePath )) {
	include $enginePath;
	$engine = new Engine ();
	$installed = true;
	$version = Engine::VERSION;
	if(@$_GET['logout']){
		$engine->auth->logout();
	}
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Thunderhawk - installer</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="">
<meta name="author" content="Carlos Alvarez - Alvarez.is">

<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css"> 
<link href="css/main.css" rel="stylesheet">
<link href="css/font-style.css" rel="stylesheet">
<link href="css/flexslider.css" rel="stylesheet">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script> 
<script src="js/jquery.flexslider.js" type="text/javascript"></script>
<script type="text/javascript" src="js/admin.js"></script>

<style type="text/css">
body {
	padding-top: 60px;
}
</style>

<!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
<!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->


<!-- Google Fonts call. Font Used Open Sans & Raleway -->
<link href="http://fonts.googleapis.com/css?family=Raleway:400,300"
	rel="stylesheet" type="text/css">
<link href="http://fonts.googleapis.com/css?family=Open+Sans"
	rel="stylesheet" type="text/css">
</head>
<body>

	<!-- NAVIGATION MENU -->

	<div class="navbar-nav navbar-inverse navbar-fixed-top">
		<div class="container">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle" data-toggle="collapse"
					data-target=".navbar-collapse">
					<span class="icon-bar"></span> <span class="icon-bar"></span> <span
						class="icon-bar"></span>
				</button>
				<a class="navbar-brand"
					href="index.php?token=<?php echo @$_SESSION['THUNDERHAWK_INSTALL']['token'] ;?>"><img
					src="images/logo30.png" alt="">THUNDERHAWK - Installer</a>
			</div>
			<div class="navbar-collapse collapse">
				<ul class="nav navbar-nav">
					<li class="active"><a
						href="index.php?token=<?php echo @$_SESSION['THUNDERHAWK_INSTALL']['token'] ;?>"><i
							class="icon-home icon-white"></i>Home</a></li>
					<li><a
						href="archive.php?token=<?php echo @$_SESSION['THUNDERHAWK_INSTALL']['token'] ;?>"><i
							class="icon-th icon-white"></i> Archive</a></li>
					<li><a
						href="database.php?token=<?php echo @$_SESSION['THUNDERHAWK_INSTALL']['token'] ;?>"><i
							class="icon-lock icon-white"></i> Database</a></li>
					<?php 
						if($installed){
							?>
							<li><a
						href="modules.php?token=<?php echo @$_SESSION['THUNDERHAWK_INSTALL']['token'] ;?>"><i
							class="icon-lock icon-white"></i> Modules</a></li>
							<li><a
						href="config.php?token=<?php echo @$_SESSION['THUNDERHAWK_INSTALL']['token'] ;?>"><i
							class="icon-lock icon-white"></i> Config</a></li>
							<li><a
						href="index.php?token=<?php echo @$_SESSION['THUNDERHAWK_INSTALL']['token'] ;?>&logout=1"><i
							class="icon-lock icon-white"></i> Logout</a></li>
							<?php 
						}
					?>
				</ul>
			</div>
			<!--/.nav-collapse -->
		</div>
	</div>

	<div class="container">
	<?php
	if(!function_exists('hash_equals')) {
		function hash_equals($str1, $str2) {
			if(strlen($str1) != strlen($str2)) {
				return false;
			} else {
				$res = $str1 ^ $str2;
				$ret = 0;
				for($i = strlen($res) - 1; $i >= 0; $i--) $ret |= ord($res[$i]);
				return !$ret;
			}
		}
	}
	if (! isset ( $_SESSION ['THUNDERHAWK_INSTALL'] )) {
		$_SESSION ['THUNDERHAWK_INSTALL'] = array (
				'time' => time (),
				'expire' => time () + 3600,
				'token' => urlencode ( base64_encode ( hash ( 'sha256', openssl_random_pseudo_bytes ( 16 ), true ) ) ) 
		);
		echo '<a class="button" href="?token=' . $_SESSION ['THUNDERHAWK_INSTALL'] ['token'] . '">Go to the installation session</a>';
		die ();
	}
	$token = urlencode ( @$_GET ['token'] );
	if (! hash_equals ( $_SESSION ['THUNDERHAWK_INSTALL'] ['token'], $token )) {
		die ( '<h3>ACCESS DENIED</h3>' );
	}
	if ($_SESSION ['THUNDERHAWK_INSTALL'] ['expire'] < time ()) {
		die ( '<h3>SESSION EXPIRED</h3>' );
	}
	
	?>
	<?php 
		function panel(){
			global $installed,$version,$engine,$user;
			?>
				  <!-- FIRST ROW -->
		<div class="row" style="margin-top: 10px;">

			<!-- USER PROFILE BLOCK -->
			<div class="col-sm-3 col-lg-3">
				<div class="dash-unit">
					<dtitle>Administrator</dtitle>
					<hr>
					<div class="thumbnail">
						<img src="images/user-icon.png" alt="Marcel Newman"
							class="img-circle">
					</div>
					<!-- /thumbnail -->
					<h1>Welcome Admin</h1>
					<h3><?php 
					if($installed){
						echo 'Thunderhawk is running...';
					}
					?></h3>
					<br>
					<div class="info-user">
					<?php 
						$link = $installed ? 'modules' : 'archive' ;
					?>
						<a href="<?php echo $link ;?>.php?token=<?php echo $_SESSION['THUNDERHAWK_INSTALL']['token'] ;?>">
						<span aria-hidden="true" class="li_settings fs1"></span>
						</a>
					</div>
				</div>
			</div>

			<!-- INFORMATION BLOCK -->
			<div class="col-sm-9 col-lg-9">
				<div class="dash-unit">
					<dtitle>Thunderhawk installer</dtitle>
					<hr>
					<div class="info-user">
						<span aria-hidden="true" class="li_display fs2"></span>
					</div>
					<br>
					<div class="text">
						<p>Thunderhawk CMS - created by Skullab.com(c) 2016.</p>
						<p>Based on Phalcon Framework</p>
						<p>Last Version : 1.0.0</p>
					</div>

				</div>
			</div>



		</div>
		<!-- /row -->
		<!-- SECOND ROW -->
		<div class="row">
			<!-- INFORMATION BLOCK -->
			<div class="col-sm-12 col-lg-12">
				<div class="dash-unit">
					<dtitle>SYSTEM INFO</dtitle>
					<hr>
					<div class="info-user">
						<span aria-hidden="true" class="li_display fs2"></span>
					</div>
					<br>
					<div class="text">
						<p>Installed : <?php if($installed){
							echo '<span style="color:green;">YES</span>' ;
						}else{
							echo '<span style="color:red;">NO</span>' ;
							}?></p>
						<p>Version : <?php echo $version ;?></p>
						<p>Modules : <?php 
						if($installed){
							foreach ($engine->getModules() as $module){
								echo '<a href="modules.php?token='.$_SESSION['THUNDERHAWK_INSTALL']['token'].'">'.$module['name'].'</a> - ';
							}
						}
						?></p>
					</div>

				</div>
			</div>
		</div>
	</div>
	<!-- /container -->
			<?php 
		}
		if($installed){
			if(!empty($_POST) && $engine->token->check()){
				
				$username = @$_POST['username'] ;
				$password = @$_POST['password'];
				try{
					$engine->auth->checkAccess(array('email'=>$username,'password'=>$password,'remember'=>false));
					$user = $engine->auth->getIdentity();
					if($user['role'] != 'Admin'){
						throw new Exception('Only ADMIN users !');
					}
					panel();
				}catch(\Exception $e){
					echo '<div class="alert alert-danger">'.$e->getMessage().'</div>';
				}
				
			}else{
				$user = $engine->auth->getIdentity();
				if($user['role'] == 'Admin'){
					panel();
				}else{
			?>
			<div class="row">
   			<div class="col-lg-offset-4 col-lg-4" style="margin-top:100px">
   			<div class="block-unit" style="text-align:center; padding:8px 8px 8px 8px;">
   				<img src="images/user-icon.png" alt="" class="img-circle">
   				<br>
   				<br>
					<form class="cmxform" id="signupForm" method="POST" action="index.php?token=<?php echo @$_SESSION ['THUNDERHAWK_INSTALL'] ['token']?>">
						<fieldset>
							<p>
								<input id="username" name="username" type="text" placeholder="Username">
								<input id="password" name="password" type="password" placeholder="Password">
								<?php echo $engine->token->generateInput(); ?>							</p>
								<input class="submit btn-success btn btn-large" type="submit" value="Login">
						</fieldset>
					</form>
   			</div>
   		</div>
        </div>
			<?php }}
		}else {
			panel();
		}
	?>
	</div>
	<!-- /container -->
	
	<div id="footerwrap">
		<footer class="clearfix"></footer>
		<div class="container">
			<div class="row">
				<div class="col-sm-12 col-lg-12">
					<p>
						<img src="images/logo.png" alt="">
					</p>
					<p>Thunderhawk CMS - Crafted With Love - Copyright 2016</p>
				</div>

			</div>
			<!-- /row -->
		</div>
		<!-- /container -->
	</div>
	<!-- /footerwrap -->

</body>
</html>
