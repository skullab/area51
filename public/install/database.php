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
?>
<!doctype html>
<html><head>
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
	<link href="http://fonts.googleapis.com/css?family=Raleway:400,300" rel="stylesheet" type="text/css">
  	<link href="http://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet" type="text/css">

  
  </head>
  <body>
  
  	<!-- NAVIGATION MENU -->

    <div class="navbar-nav navbar-inverse navbar-fixed-top">
        <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="index.php?token=<?php echo $_SESSION['THUNDERHAWK_INSTALL']['token'] ;?>"><img src="images/logo30.png" alt="">THUNDERHAWK - Installer</a>
        </div> 
          <div class="navbar-collapse collapse">
            <ul class="nav navbar-nav">
              <li><a href="index.php?token=<?php echo $_SESSION['THUNDERHAWK_INSTALL']['token'] ;?>"><i class="icon-home icon-white"></i>Home</a></li>                            
              <li><a href="archive.php?token=<?php echo $_SESSION['THUNDERHAWK_INSTALL']['token'] ;?>"><i class="icon-th icon-white"></i> Archive</a></li>
              <li class="active"><a href="database.php?token=<?php echo $_SESSION['THUNDERHAWK_INSTALL']['token'] ;?>"><i class="icon-lock icon-white"></i> Database</a></li>
              <?php 
						if($installed){
							?>
							<li><a
						href="modules.php?token=<?php echo $_SESSION['THUNDERHAWK_INSTALL']['token'] ;?>"><i
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
          </div><!--/.nav-collapse -->
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
					'expire' => time () + (60 * 60),
					'token' => urlencode ( base64_encode ( hash ( 'sha256', openssl_random_pseudo_bytes ( 16 ), true ) ) ) 
			);
			echo '<a href="?token=' . $_SESSION ['THUNDERHAWK_INSTALL'] ['token'] . '">GO TO INSTALL PANEL</a>';
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
	$test = false ;
	$dbOK = false ;
	if($installed && isset($_POST['dbsuccess']) && $engine->token->check()){
		// create db ini
		$ini = fopen(CORE_PATH.'config/db.ini.php','w');
		if($ini){
			fwrite($ini,';<?php die(); __halt_compiler(); ?>'.PHP_EOL);
			fwrite($ini, PHP_EOL);
			fwrite($ini, '[db]'.PHP_EOL);
			fwrite($ini, PHP_EOL);
			$config = array(
				'adapter' => 'Mysql',
				'host' => $_POST['dbhost'],
				'dbname' => $_POST['dbname'],
				'username' => $_POST['dbuser'],
				'password' => $_POST['dbpass'],
				'table.prefix' => $_POST['dbprefix']
			);
			foreach ($config as $k => $v){
				fwrite($ini, $k.' = "'.$v.'"'.PHP_EOL);
			}
			fclose($ini);
			$dbOK = true ;
		}
	}
	if(!empty($_POST)){
		//var_dump('ok');
		$config = array(
				'host' => $_POST['dbhost'],
				'dbname' => $_POST['dbname'],
				'username' => $_POST['dbuser'],
				'password' => $_POST['dbpass']
		);
		
		try{
			$db = new Phalcon\Db\Adapter\PDO\Mysql($config);
			$test = true ;
		}catch (\Exception $e){
			$test = false ;
		}
		if($test === false){
				echo '<div class="alert alert-danger">Connection Failed</div>' ;
		}else{
			if($dbOK){
				$sql = file(APP_PATH.'schema/export.sql');
				// Temporary variable, used to store current query
				$templine = '';
				// Loop through each line
				foreach ($sql as $line)
				{
					// Skip it if it's a comment
					if (substr($line, 0, 2) == '--' || $line == '')
						continue;
				
						// Add this line to the current segment
						$templine .= $line;
						// If it has a semicolon at the end, it's the end of the query
						if (substr(trim($line), -1, 1) == ';')
						{
							// Perform the query
							try{
								$db->execute($templine);
							}catch (\Exception $e){
								echo('Error performing query \'<strong>' . $templine . '\': ' . mysql_error() . '<br /><br />');
							}
							// Reset temp variable to empty
							$templine = '';
						}
				}
				echo '<div class="alert alert-success">The configuration file is stored !</div>' ;
				echo '<div class="alert alert-success">Database is created !</div>' ;
				echo '<div class="alert alert-success">
      						The user : admin@thunderhawk.com<br>
      						with the password : root<br>
      						is created ! Please change this credentials ASAP !
      					</div>' ;
			}else{
				echo '<div class="alert alert-success">Connection OK</div>' ;
			}
		}
		
	}
	?>
	  <!-- FIRST ROW OF BLOCKS -->    
      <div class="row">
      
      <div class="col-lg-offset-4 col-lg-4" style="margin-top:10px">
   			<div class="block-unit" style="text-align:center; padding:8px 8px 8px 8px;">
					<?php 
					if($installed && !file_exists(CORE_PATH.'config/db.ini.php')){
						?>
						<form class="cmxform" id="signupForm" method="POST" action="database.php?token=<?php echo $_SESSION['THUNDERHAWK_INSTALL']['token'] ;?>">
						<fieldset>
							<p>
								<label><h5>Host</h5></label>
								<input type="text" name="dbhost" value="<?php echo @$_POST['dbhost']?>">
								<label><h5>Username</h5></label>
								<input type="text" name="dbuser" value="<?php echo @$_POST['dbuser']?>">
								<label><h5>Password</h5></label>
								<input type="text" name="dbpass" value="<?php echo @$_POST['dbpass']?>">
								<label><h5>DB Name</h5></label>
								<input type="text" name="dbname" value="<?php echo @$_POST['dbname']?>">
								<label><h5>Table prefix</h5></label>
								<input type="text" name="dbprefix" value="<?php echo @$_POST['dbprefix']?>">
								<?php echo $engine->token->generateInput();?>
								
							</p>
							<input name="testdb" type="submit" 
							class="submit btn-warning btn btn-large" 
							value="<?php if($test){echo 'Submit';}else{ echo 'Test connection';}?>">
							<?php if($test){
								echo '<input type="hidden" name="dbsuccess" value="ok">' ;
							}?>
						</fieldset>
					</form>
						<?php 
					}else{
						if($installed){
						?>
						<h5 style="margin-top:100px;">Thunderhawk DB is already installed !</h5>
						<?php
						}else{
							?>
							<h5 style="margin-top:100px;">You need to install Thunderhawk before !</h5>
							<?php
						}
					}
					?>
					
					
   			</div>

   		</div>	
      </div>

	</div> <!-- /container -->
	<div id="footerwrap">
      	<footer class="clearfix"></footer>
      	<div class="container">
      		<div class="row">
      			<div class="col-sm-12 col-lg-12">
      			<p><img src="images/logo.png" alt=""></p>
      			<p>Thunderhawk CMS - Crafted With Love - Copyright 2016</p>
      			</div>

      		</div><!-- /row -->
      	</div><!-- /container -->		
	</div><!-- /footerwrap -->
          
</body></html>

