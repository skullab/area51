<?php
use Thunderhawk\API\Engine;
session_start ();
$enginePath = __DIR__ . '/../../core/API/Engine.php';
$installed = false;
$version = null;
$engine = null;
if (file_exists ( $enginePath )) {
	include $enginePath;
	$engine = new Engine ();
	$installed = true;
	$version = Engine::VERSION;
}

if(!empty($_POST)){
	if(@$_POST['submitConfig'] && $engine->token->check()){
		$ret = file_put_contents(CORE_PATH.'config/app.ini.php', $_POST['app']);
		$ret &= file_put_contents(CORE_PATH.'config/dirs.ini.php', $_POST['dirs']);
		//$ret &= file_put_contents(CORE_PATH.'config/db.ini.php', $_POST['db']);
		if($ret){
			$message = 'Files saved !';
		}else{
			$message = 'Something gone wrong...';
		}
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
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.2.0/styles/default.min.css">
<script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.2.0/highlight.min.js"></script>
<style type="text/css">
body {
	padding-top: 60px;
}
textarea {
	height:205px;
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
					<li><a
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
							<li class="active"><a
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
	<div class="row" style="margin-top: 10px;">
	<?php 
		if($installed){
			$auth = $engine->auth->getIdentity();
			if($auth['role'] == 'Admin'){
				if(@$message){
					?>
					<div class="alert alert-warning"><?php echo $message ;?></div>
					<?php 
				}
				?>
				<form name="configForm" method="POST" action="config.php?token=<?php echo $_SESSION['THUNDERHAWK_INSTALL']['token'];?>">
				<fieldset>
					<p>
					<label><h5>app</h5></label>
					<textarea name="app"><?php echo file_get_contents(CORE_PATH.'config/app.ini.php');?></textarea>
					
					<label><h5>dirs</h5></label>
					<textarea name="dirs"><?php echo file_get_contents(CORE_PATH.'config/dirs.ini.php');?></textarea>
					
					<?php echo $engine->token->generateInput();?>
					</p>
					<input type="submit" name="submitConfig" class="submit" value="Save">
				</fieldset>
				</form>
				<?php 
			}else {
			?>
				<div class="col-lg-offset-4 col-lg-4" style="margin-top: 10px">
				<div class="block-unit"
					style="text-align: center; padding: 8px 8px 8px 8px;">
					<h5 style="margin-top: 100px;">You need to login before !</h5>
				</div>
			</div>
				<?php
		}
	} else {
		?>
			<div class="col-lg-offset-4 col-lg-4" style="margin-top: 10px">
				<div class="block-unit"
					style="text-align: center; padding: 8px 8px 8px 8px;">
					<h5 style="margin-top: 100px;">You need to install Thunderhawk
						before !</h5>
				</div>
			</div>
			<?php
	}
	?>
	</div>
	</div>
	
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
