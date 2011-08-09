<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo str_replace('_','-',$sf_user->getCulture()); ?>">
<head>
	<meta charset="UTF-8" />
	<title><?php echo sfContext::getInstance()->getResponse()->getTitle(ESC_RAW); ?></title>
	<?php include_title() ?> 
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<link rel="icon" type="image/vnd.microsoft.icon" sizes="16x16 32x32" href="/favicon.ico" />
	<link rel="icon" type="image/png" sizes="32x32" href="/favicon.png" />
	<link rel="alternate" type="application/atom+xml" title="Newest team matches" href="<?php //echo url_for('@api_contest_feed?sf_format=atom', true); ?>" />
	<link rel="search" type="application/opensearchdescription+xml" title="Sport Service Search" href="<?php //echo url_for('@opensearch_description?sf_format=xml', true); ?>" />
	<link rel='stylesheet' type='text/css' href='http://fonts.googleapis.com/css?family=Droid+Sans|Droid+Sans+Mono' />
	<?php include_stylesheets() ?> 
	<!--[if lt IE 9]>
		<script type="text/javascript" src="http://ie7-js.googlecode.com/svn/version/2.1(beta4)/IE9.js"></script>
	<![endif]-->
	<?php include_javascripts() ?>
	<?php if (has_slot('headlinks')) { include_slot('headlinks'); } ?>
</head>
<body>
	<header>
	<h1><img src="/images/bvbb_logo.jpg" alt="BVBB Logo" title="Badminton-Verband Berlin-Brandenburg" width="150" height="133" />
	Ergebnisdienst</h1>
	</header>
		
	<section>
	<?php echo $sf_content ?>
	</section>
</body>
</html>
