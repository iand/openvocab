<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN"
   "http://www.w3.org/TR/html4/strict.dtd">

<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    
    <?php
      echo '<title>Login to '. htmlspecialchars(config_item('site_name')) . '</title>' . "\n";
    ?>

    <!-- Framework CSS -->
    <link rel="stylesheet" href="/css/blueprint/screen.css" type="text/css" media="screen, projection">
    <link rel="stylesheet" href="/css/blueprint/print.css" type="text/css" media="print">
    <!--[if lt IE 8]><link rel="stylesheet" href="/css/blueprint/ie.css" type="text/css" media="screen, projection"><![endif]-->

    <style type="text/css">
      .alt { 
        color: #666; 
        font-family: "Warnock Pro", "Goudy Old Style","Palatino","Book Antiqua", Georgia, serif; 
        font-style: italic;
        font-weight: normal;
      }    
    </style>     
  </head>
  <body>
    <div class="container">
    <?php require_once('header.inc.php'); ?>
    <h2 class="bottom">Login</h2>
    <hr>

    <?php if (isset($msg)) { echo "<div class=\"alert\">$msg</div>"; } ?>
    <?php if (isset($error)) { echo "<div class=\"error\">$error</div>"; } ?>
    <?php if (isset($success)) { echo "<div class=\"success\">$success</div>"; } ?>


    <div id="verify-form" class="span-15 colborder">
      <form method="post" action="/login">
        Identity&nbsp;URL:
        <input type="hidden" name="action" value="verify" />
        <input type="text" name="openid_identifier" value="" />
        <input type="submit" value="Verify" />
      </form>
    </div>
      <div class="span-8 last">

      </div>
      <hr class="space">
      <hr>
      <div id="footer" class="quiet append-bottom">
        <?php echo config_item('footer_html'); ?>
      </div>
    </div>
  </body>
</html>




