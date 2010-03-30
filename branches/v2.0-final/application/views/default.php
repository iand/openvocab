<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN"
   "http://www.w3.org/TR/html4/strict.dtd">

<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <?php if (isset($description)) { ?>
    <meta name="description" content="<?php e($description); ?>">
    <?php } ?>
    <title>Blueprint Sample Page</title>

    <!-- Framework CSS -->
    <link rel="stylesheet" href="/css/blueprint/screen.css" type="text/css" media="screen, projection">
    <link rel="stylesheet" href="/css/blueprint/print.css" type="text/css" media="print">
    <!--[if lt IE 8]><link rel="stylesheet" href="/css/blueprint/ie.css" type="text/css" media="screen, projection"><![endif]-->

    <!-- Import fancy-type plugin for the sample page. -->
    <link rel="stylesheet" href="/css/blueprint/plugins/fancy-type/screen.css" type="text/css" media="screen, projection">
<?php
  if (isset($links)) {
    foreach ($links as $link) {
      echo '    <link rel="alternate" type="' . htmlspecialchars($link['type']) . '" href="' . htmlspecialchars($link['href']) . '" title="' . htmlspecialchars($link['title']) . '">' . "\n";
    }
  }
?>       
  </head>
  <body>
    <div class="container">
      <h1>
      <?php 
        if (isset($title)) {
         echo(htmlspecialchars($title));
        }
      ?>
    </h1>
      <hr>
      <?php 
        if (isset($subtitle)) {
          echo '<h2 class="alt">' . htmlspecialchars($subtitle) . '</h2><hr>';
        }
      ?>
      <div class="span-7 colborder">
        <h6>Here's a box</h6>
        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip.</p>
      </div>
      <div class="span-8 colborder">
        <h6>And another box</h6>
        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat laboris nisi ut aliquip.</p>
      </div>
      <div class="span-7 last">
        <h6>This box is aligned with the sidebar</h6>
        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip.</p>
      </div>
      <hr>
      <hr class="space">
      <div class="span-15 prepend-1 colborder">
        <?php if (isset($content)) { echo $content; } ?>
      </div>
      <div class="span-7 last">
        <?php if (isset($sidebar)) { echo $sidebar; } ?>
      </div>
      <hr>
      <h2 class="alt">You may pick and choose amongst these and many more features, so be bold.</h2>
      <hr>
      <p>
        <a href="http://validator.w3.org/check?uri=referer">
          <img src="valid.png" alt="Valid HTML 4.01 Strict" height="31" width="88" class="top">
        </a>
      </p>
    </div>
  </body>
</html>
