<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN"
   "http://www.w3.org/TR/html4/strict.dtd">

<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">

    <?php
      echo '<title>Recent changes to the ' . htmlspecialchars(config_item('vocab_name')) . ' RDF schema</title>' . "\n";
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
    <h2 class="bottom">Recent Changes</h2>
    <hr>
      <div class="span-24">

      <p>The 30 most recent changes to the <?= htmlspecialchars(config_item('vocab_name')); ?> RDF schema:</p>
        <ul>
        <?php
          foreach ($model->changes as $change_uri) {
            $terms = $model->graph->get_subjects_where_resource('http://www.w3.org/2004/02/skos/core#note', $change_uri);
            $term_qname = $model->graph->uri_to_qname($terms[0]);

            $label = $model->graph->get_first_literal($change_uri, RDFS_LABEL);
            $comment = $model->graph->get_first_literal($change_uri, RDFS_COMMENT);
            $date = $model->graph->get_first_literal($change_uri, 'http://purl.org/dc/elements/1.1/created');
            $creator_uri = $model->graph->get_first_resource($change_uri, DC_CREATOR);
            $openid = $model->graph->get_first_resource($creator_uri, 'http://xmlns.com/foaf/0.1/openid');

            printf('<li><span class="date">%s</span>: <a href="%s">%s</a> was %s by %s <a href="%s" class="details">details</a></li>' . "\n", htmlspecialchars($date), htmlspecialchars(local_link($terms[0])), htmlspecialchars($term_qname), htmlspecialchars($label), htmlspecialchars($openid), htmlspecialchars(local_link($change_uri)));
          }
        ?>
        </ul>
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

