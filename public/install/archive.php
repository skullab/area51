<?php
set_time_limit(0);

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

if (@$_FILES ['archive'] ['name']) {
	$filename = $_FILES ["archive"] ["name"];
	$source = $_FILES ["archive"] ["tmp_name"];
	$type = $_FILES ["archive"] ["type"];
	
	$name = explode ( ".", $filename );
	$accepted_types = array (
			'application/zip',
			'application/x-zip-compressed',
			'multipart/x-zip',
			'application/x-compressed' 
	);
	foreach ( $accepted_types as $mime_type ) {
		if ($mime_type == $type) {
			$okay = true;
			break;
		}
	}
	
	$continue = strtolower ( $name [count ( $name ) - 1] ) == 'zip' ? true : false;
	if (! $continue) {
		$message = "The file you are trying to upload is not a .zip file. Please try again.";
	} else {
		
		$target_path = __DIR__ . '/../../' . $filename; // change this to the correct site path
		if (move_uploaded_file ( $source, $target_path )) {
			$zip = new ZipArchive ();
			$x = $zip->open ( $target_path );
			if ($x === true) {
				$zip->extractTo ( __DIR__ . '/../../' ); // change this to the correct site path
				$zip->close ();
				
				unlink ( $target_path );
			}
			$message = "Your .zip file was uploaded and unpacked.";
		} else {
			$message = "There was a problem with the upload. Please try again.";
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

<link rel="stylesheet"
	href="http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
<link href="css/main.css" rel="stylesheet">
<link href="css/font-style.css" rel="stylesheet">
<link href="css/flexslider.css" rel="stylesheet">
<script
	src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<script
	src="http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
<script src="js/jquery.flexslider.js" type="text/javascript"></script>
<script type="text/javascript" src="js/admin.js"></script>


<script type="text/javascript" src="js/admin.js"></script>

<style type="text/css">
body {
	padding-top: 60px;
}

#bar_blank {
	border: solid 1px #000;
	height: 20px;
	width: 300px;
}

#bar_color {
	background-color: #006666;
	height: 20px;
	width: 0px;
}

#bar_blank, #hidden_iframe {
	display: none;
}
#status{
	color:red;
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
					href="index.php?token=<?php echo $_SESSION['THUNDERHAWK_INSTALL']['token'] ;?>"><img
					src="images/logo30.png" alt="">THUNDERHAWK - Installer</a>
			</div>
			<div class="navbar-collapse collapse">
				<ul class="nav navbar-nav">
					<li><a
						href="index.php?token=<?php echo $_SESSION['THUNDERHAWK_INSTALL']['token'] ;?>"><i
							class="icon-home icon-white"></i>Home</a></li>
					<li class="active"><a
						href="archive.php?token=<?php echo $_SESSION['THUNDERHAWK_INSTALL']['token'] ;?>"><i
							class="icon-th icon-white"></i> Archive</a></li>
					<li><a
						href="database.php?token=<?php echo $_SESSION['THUNDERHAWK_INSTALL']['token'] ;?>"><i
							class="icon-lock icon-white"></i> Database</a></li>
              <?php
														if ($installed) {
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
			</div>
			<!--/.nav-collapse -->
		</div>
	</div>

	<div class="container">
	<?php
	if (! function_exists ( 'hash_equals' )) {
		function hash_equals($str1, $str2) {
			if (strlen ( $str1 ) != strlen ( $str2 )) {
				return false;
			} else {
				$res = $str1 ^ $str2;
				$ret = 0;
				for($i = strlen ( $res ) - 1; $i >= 0; $i --)
					$ret |= ord ( $res [$i] );
				return ! $ret;
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
	  <!-- FIRST ROW OF BLOCKS -->
		<div class="row">
			<div class="col-lg-offset-4 col-lg-4" style="margin-top: 100px">
				<div class="block-unit"
					style="text-align: center; padding: 8px 8px 8px 8px;">
					
					<?php
					
if (@$message) {
						echo '<div class="alert alert-warning">' . $message . '</div>';
					}
					?>
					<?php
					if (! $installed) {
						
						?>
						<div id="bar_blank">
						<div id="bar_color"></div>
						</div>
						<div id="status"></div>
					<form enctype="multipart/form-data" class="cmxform" id="uploadForm"
						method="POST"
						action="archive.php?token=<?php echo @$_SESSION ['THUNDERHAWK_INSTALL'] ['token'];?>">
						<fieldset>
							<p>
								<label><h5>
										Upload Thunderhawk Archive
										<p>upload only a zip file</p>
									</h5></label> <input id="archive" name="archive" type="file"
									style="color: red;"> <input type="hidden"
									value="uploadForm"
									name="<?php echo ini_get("session.upload_progress.name"); ?>">

							</p>
							<input class="submit btn-success btn btn-large" type="submit"
								value="Upload">
						</fieldset>
					</form>
					<iframe id="hidden_iframe" name="hidden_iframe" src="about:blank"></iframe>
					<script type="text/javascript">
					function toggleBarVisibility() {
					    var e = document.getElementById("bar_blank");
					    e.style.display = (e.style.display == "block") ? "none" : "block";
					}

					function createRequestObject() {
					    var http;
					    if (navigator.appName == "Microsoft Internet Explorer") {
					        http = new ActiveXObject("Microsoft.XMLHTTP");
					    }
					    else {
					        http = new XMLHttpRequest();
					    }
					    return http;
					}

					function sendRequest() {
					    var http = createRequestObject();
					    http.open("GET", "progress.php");
					    http.onreadystatechange = function () { handleResponse(http); };
					    http.send(null);
					}

					function handleResponse(http) {
					    var response;
					    if (http.readyState == 4) {
					        response = http.responseText;
					        document.getElementById("bar_color").style.width = response + "%";
					        document.getElementById("status").innerHTML = response + "%";
					        if (response < 100) {
					            setTimeout("sendRequest()", 1000);
					        }
					        else {
					            toggleBarVisibility();
					            document.getElementById("status").innerHTML = "Done.";
					        }
					    }
					}

					function startUpload() {
					    toggleBarVisibility();
					    setTimeout("sendRequest()", 1000);
					}

					(function () {
					    document.getElementById("uploadForm").onsubmit = startUpload;
					})();
					</script>
						<?php
					} else {
						?>
						<h5>Thunderhawk is already installed !</h5>
						<?php
					}
					?>
					
					
   			</div>

			</div>
		</div>

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
