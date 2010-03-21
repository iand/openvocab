<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN"
   "http://www.w3.org/TR/html4/strict.dtd">

<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">

    <?php
      echo '<title>Terms in the ' . htmlspecialchars(config_item('vocab_name')) . ' RDF schema</title>' . "\n";

      $desc = '';
      if ($model->comment) $desc .= 'Definition: ' . $model->comment;


      if ($desc) {
        echo '<meta name="description" content="' . htmlspecialchars($desc) . '" />' . "\n";
      }
    ?>

    <!-- Framework CSS -->
    <link rel="stylesheet" href="/css/blueprint/screen.css" type="text/css" media="screen, projection">
    <link rel="stylesheet" href="/css/blueprint/print.css" type="text/css" media="print">
    <!--[if lt IE 8]><link rel="stylesheet" href="/css/blueprint/ie.css" type="text/css" media="screen, projection"><![endif]-->
    <link rel="stylesheet" href="/css/screen.css" type="text/css" media="screen, projection">
    <?php if(isset($js)) echo $js; ?>

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
    <?php require_once('header.inc.php'); ?>
    <h2 class="top bottom">Terms</h2>
    <hr>
      <div class="span-24">

      <p>The following classes and properties have been defined as part of the <?= htmlspecialchars(config_item('vocab_name')); ?> RDF schema:</p>
      </div>
      <div class="span-11 colborder">
        <h3>Properties</h3>
        <?php
          foreach ($model->properties as $uri) {
            echo '<p class="term"><span class="label"><a href="' . htmlspecialchars(local_link($uri)) . '">' . htmlspecialchars($model->graph->get_label($uri)) . '</a></span>';
            echo '<br /><span class="uri">' . htmlspecialchars($uri) . '</span>';
            $comment = $model->graph->get_first_literal($uri, RDFS_COMMENT);
            if ($comment) echo '<br /><span class="comment">' . htmlspecialchars($comment). '</span>';
            echo '</p>';
          }
        ?>
      </div>

      <div class="span-11 last">
        <h3>Classes</h3>
        <?php
          foreach ($model->classes as $uri) {
            echo '<p class="term"><span class="label"><a href="' . htmlspecialchars(local_link($uri)) . '">' . htmlspecialchars($model->graph->get_label($uri)) . '</a></span>';
            echo '<br /><span class="uri">' . htmlspecialchars($uri) . '</span>';
            $comment = $model->graph->get_first_literal($uri, RDFS_COMMENT);
            if ($comment) echo '<br /><span class="comment">' . htmlspecialchars($comment). '</span>';
            echo '</p>';
          }
        ?>
      </div>

      <?php
        if (isset($links)) {
          echo '<div class="span-24  append-bottom">Get the data for this page: ';
          $done_first = 0;
          foreach ($links as $link) {
            if ($done_first) echo ', ';
            echo '<a type="' . htmlspecialchars($link['type']) . '" href="' . htmlspecialchars($link['href']) . '" title="' . htmlspecialchars($link['title']) . '">' . htmlspecialchars($link['title']) . "</a>";
            $done_first = 1;
          }
          echo '</div>';
        }
      ?>
      <hr>

      <div id="footer" class="quiet append-bottom">
        <?php echo config_item('footer_html'); ?>
      </div>
    </div>
  </body>
</html>

