<?php header("HTTP/1.1 404 Not Found"); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN"
   "http://www.w3.org/TR/html4/strict.dtd">

<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">

    <?php
      echo '<title>Not Found</title>' . "\n";
    ?>

    <!-- Framework CSS -->
    <link rel="stylesheet" href="/css/blueprint/screen.css" type="text/css" media="screen, projection">
    <link rel="stylesheet" href="/css/blueprint/print.css" type="text/css" media="print">
    <!--[if lt IE 8]><link rel="stylesheet" href="/css/blueprint/ie.css" type="text/css" media="screen, projection"><![endif]-->
    <link rel="stylesheet" href="/css/screen.css" type="text/css" media="screen, projection">
    <?php if(isset($js)) echo $js; ?>
  </head>
  <body>
    <div class="container">
    <?php require_once(dirname(__FILE__) .'/../views/header.inc.php'); ?>
    <h2 class="bottom"><?php echo $heading; ?></h2>
    <hr>
      <div class="span-24 last">
        <?php if (isset($msg)) { echo "<div class=\"alert\">$msg</div>"; } ?>
        <?php if (isset($error)) { echo "<div class=\"error\">$error</div>"; } ?>
        <?php if (isset($success)) { echo "<div class=\"success\">$success</div>"; } ?>

        <?php echo $message; ?>

      </div>
      <hr>
      <div id="footer" class="quiet append-bottom">
        <?php echo config_item('footer_html'); ?>
      </div>
    </div>
  </body>
</html>

