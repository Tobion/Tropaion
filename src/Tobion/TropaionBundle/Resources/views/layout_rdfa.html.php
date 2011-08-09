<?php echo '<?xml version="1.0" encoding="UTF-8" ?>' . "\n"; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML+RDFa 1.0//EN" "http://www.w3.org/MarkUp/DTD/xhtml-rdfa-1.dtd">
<html version="XHTML+RDFa 1.0" xml:lang="<?php echo str_replace('_','-',$sf_user->getCulture()); ?>"
	xmlns="http://www.w3.org/1999/xhtml" 
	xmlns:xsd="http://www.w3.org/2001/XMLSchema#"
	xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
	xmlns:rdfs="http://www.w3.org/2000/01/rdf-schema#"
	xmlns:owl="http://www.w3.org/2002/07/owl#"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	xmlns:cal="http://www.w3.org/2002/12/cal#"
	xmlns:foaf="http://xmlns.com/foaf/0.1/"
	xmlns:og="http://opengraphprotocol.org/schema/"
	xmlns:sport="http://purl.org/sport/ontology/"
	xmlns:resource="<?php echo sfContext::getInstance()->getRequest()->getUriPrefix(); // see route @resource_access ?>/resource/">
<head>
	<title property="dc:title"><?php echo sfContext::getInstance()->getResponse()->getTitle(); ?></title>
	<meta name="generator" content="Sport Service" />
	<link rel="shortcut icon" type="image/vnd.microsoft.icon" href="/favicon.ico" />
	<link rel="icon" type="image/png" href="/favicon.png" />
	<link rel="alternate" type="application/atom+xml" title="Newest contests" href="<?php //echo url_for('@api_contest_feed?sf_format=atom', true); ?>" />
	<link rel="search" type="application/opensearchdescription+xml" title="Sport Service Search" href="<?php //echo url_for('@opensearch_description?sf_format=xml', true); ?>" />
	<link rel='stylesheet' type='text/css' href='http://fonts.googleapis.com/css?family=Droid+Sans|Droid+Sans+Mono' />
	<?php include_stylesheets() ?> 
	<!--[if lt IE 9]>
		<script src="http://ie7-js.googlecode.com/svn/version/2.1(beta4)/IE9.js"></script>
	<![endif]-->
	<?php include_javascripts() ?>
	<?php if (has_slot('headlinks')) { include_slot('headlinks'); } ?>
</head>
<body>
	<div>
	<h1><img src="/images/bvbb_logo.jpg" alt="BVBB Logo" title="Badminton-Verband Berlin-Brandenburg" width="150" height="133" />
	Ergebnisdienst</h1>
	</div>
	
	<?php include_component('teammatch', 'menu') ?>

	<div>
	<?php echo $sf_content ?>
	</div>
	
	<p class="license" about="http://www.tobion.de/sportservice/software/" xmlns:doap="http://usefulinc.com/ns/doap#" typeof="doap:Project">
		<span href="http://purl.org/dc/dcmitype/InteractiveResource" property="dc:title" rel="dc:type">Sport Service</span> <?php echo __('by'); ?> 
		<a xmlns:cc="http://creativecommons.org/ns#" href="http://www.tobion.de/" property="cc:attributionName" rel="cc:attributionURL" xml:lang="de">Tobias Schultze</a> <?php echo __('is licensed under'); ?> 
		<a rel="license" href="http://creativecommons.org/licenses/by/3.0/de/">Creative Commons Attribution 3.0 Germany License</a>. 
		<a rel="license" href="http://creativecommons.org/licenses/by/3.0/de/"><img alt="Creative Commons License" style="border-width:0" src="http://i.creativecommons.org/l/by/3.0/de/80x15.png" /></a>
	</p>
	
	<p class="validation" about="" resource="http://www.w3.org/TR/rdfa-syntax" rel="dct:conformsTo" xmlns:dct="http://purl.org/dc/terms/">
		<?php echo __('Semantically annotated using'); ?> <a href="http://validator.w3.org/check?uri=referer"><img src="http://www.w3.org/Icons/valid-xhtml-rdfa-blue" width="88" height="31" alt="Valid XHTML + RDFa" /></a>
	</p>
</body>
</html>
