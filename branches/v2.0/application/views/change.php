<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN"
   "http://www.w3.org/TR/html4/strict.dtd">

<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">

    <?php
      echo '<title>A change to the ' . htmlspecialchars(config_item('vocab_name')) . ' RDF schema</title>' . "\n";
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
    <h2 class="bottom">Change Details</h2>
    <hr>
      <div class="span-15 colborder">
      <?php
        echo '<p class="term"><strong>Term changed: </strong><a href="' . htmlspecialchars(local_link($model->term)) . '">' . htmlspecialchars($model->graph->get_label($model->term)) . '</a></span></p>';

        printf('<p><strong>Date of change:</strong> %s</p>' . "\n", htmlspecialchars($model->date));

        if ($model->reason) {
          echo '<p><strong>Reason for change: </strong>'. htmlspecialchars($model->reason) . '</p>';
        }

        if ($model->before) {
          printf('<h3>Before</h3><pre>%s</pre>' . "\n", htmlspecialchars($model->before));
        }

        if ($model->after) {
          printf('<h3>After</h3><pre>%s</pre>' . "\n", htmlspecialchars($model->after));
        }
      ?>
      </div>

      <div class="span-8 last">
        <?php
          if (isset($links)) {
            echo '<p>Other formats: <ul>';
            foreach ($links as $link) {
              echo '<li><a type="' . htmlspecialchars($link['type']) . '" href="' . htmlspecialchars($link['href']) . '" title="' . htmlspecialchars($link['title']) . '">' . htmlspecialchars($link['title']) . "</a></li>";
            }
            echo '</ul></p>';
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

