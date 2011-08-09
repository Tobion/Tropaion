<?php echo '<?xml version="1.0" encoding="UTF-8" ?>' . "\n"; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
  <head>
    <?php include_http_metas() ?>
    <?php include_title() ?>
    <?php include_metas() ?>
    <link rel="shortcut icon" type="image/x-icon" href="/favicon.ico" />
    <link rel="icon" type="image/png" href="/favicon.png" />
    <link rel="alternate" type="application/atom+xml" title="Newest contests" href="<?php echo url_for('@api_contest_feed?sf_format=atom', true); ?>" />
    <link rel="search" type="application/opensearchdescription+xml" title="Sport Service Search" href="<?php echo url_for('@opensearch_description?sf_format=xml', true); ?>" />
  </head>
  <body>
    <?php echo $sf_content ?>
  </body>
</html>
