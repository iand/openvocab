<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN"
   "http://www.w3.org/TR/html4/strict.dtd">

<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">

    <?php
      echo '<title>'. htmlspecialchars(config_item('site_name')) . '</title>' . "\n";
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
    <?php require_once('header.inc.php'); ?>
    <h2 class="bottom">Welcome</h2>
    <hr>

    <?php if (isset($msg)) { echo "<div class=\"alert\">$msg</div>"; } ?>
    <?php if (isset($error)) { echo "<div class=\"error\">$error</div>"; } ?>
    <?php if (isset($success)) { echo "<div class=\"success\">$success</div>"; } ?>


    <div id="content" class="span-15 colborder">

    </div>
      <div class="span-8 last">
        <div class="box">
          <h2>News</h2>
          <p>Read the <a href="http://blog.iandavis.com/2008/12/introducing-openvocab">blog post</a> introducing OpenVocab.</p>

        </div>
        <div class="box">
          <h2>About</h2>
          <p>OpenVocab is a community maintained vocabulary intended for use on the Semantic Web, ideal for properties and classes that don't warrant the effort of creating or maintaining a full schema. OpenVocab allows anyone to create and modify vocabulary terms using their web browser. Each term is described using appropriate elements of RDF, RDFS and OWL. OpenVocab allows you to create any properties and classes; assign labels, comments and descriptions; declare domains and ranges and much more.</p>
        </div>
      </div>
      <hr class="space">
      <hr>
      <div id="footer" class="quiet append-bottom">
        <?php echo config_item('footer_html'); ?>
      </div>
    </div>
  </body>
</html>




