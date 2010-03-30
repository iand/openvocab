<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN"
   "http://www.w3.org/TR/html4/strict.dtd">

<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Create terms</title>
    <meta name="description" content="Anyone can participate in creating the <?= htmlspecialchars(config_item('vocab_name')); ?> schema" />

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
    <h2 class="bottom">Create Terms</h2>
    <hr>
      <div class="span-24">
      <p>Anyone can participate in creating the <?= htmlspecialchars(config_item('vocab_name')); ?> RDF schema</p>

      <?php
        if ($this->session->userdata('logged_in') ) {
      ?>
        <ul>
          <li><a href="/forms/newprop">Create a new property</a></li>
          <li><a href="/forms/newclass">Create a new class</a></li>
        </ul>        
      <?php
        }
        else {
          echo '<p><a href="/login">Login to create terms</a></p>';
        }
      ?>
      
      </div>

      <hr>
      <div id="footer" class="quiet append-bottom">
        <?php echo config_item('footer_html'); ?>
      </div>
    </div>
  </body>
</html>

