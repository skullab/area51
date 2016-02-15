<!DOCTYPE html>
<html lang="<?php if (isset($lang)) { ?><?php echo $lang; ?><?php } else { ?>it<?php } ?>">
	<head>
		<?php echo $this->tag->getTitle(); ?>
		<!-- BEGIN META -->
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="keywords" content="your,keywords">
		<meta name="description" content="Short explanation about this website">
		<!-- END META -->

		<!-- BEGIN STYLESHEETS -->
		<?php echo $this->assets->outputCss('css-header'); ?>
		<!-- END STYLESHEETS -->

		<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!--[if lt IE 9]>
			<script type="text/javascript" src="http://www.codecovers.eu/assets/js/modules/materialadmin/libs/utils/html5shiv.js?1422823601"></script>
			<script type="text/javascript" src="http://www.codecovers.eu/assets/js/modules/materialadmin/libs/utils/respond.min.js?1422823601"></script>
    	<![endif]-->
    	
	<body class="menubar-hoverable header-fixed ">
	<?php echo $this->getContent(); ?>
	</body>
</html>
