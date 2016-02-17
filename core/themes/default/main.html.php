<?php echo $this->tag->getDoctype(); ?>
<html lang="it">
	<head>
		<?php echo $this->tag->getTitle(); ?>

		<!-- BEGIN META -->
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="keywords" content="your,keywords">
		<meta name="description" content="Short explanation about this website">
		<!-- END META -->

		<!-- BEGIN STYLESHEETS -->
		<?php echo $this->assets->outputCss(); ?>
		<!-- END STYLESHEETS -->

		<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!--[if lt IE 9]>
		<?php echo $this->assets->outputJs('ie'); ?>
		<![endif]-->
	</head>
	<body class="menubar-hoverable header-fixed menubar-pin ">

		<?php echo $this->getContent(); ?>

		<!-- BEGIN JAVASCRIPT -->
		<?php echo $this->assets->outputJs(); ?>
		<!-- END JAVASCRIPT -->

	</body>
</html>
