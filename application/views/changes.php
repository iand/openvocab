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
      <table>
        <tr>
          <th>Date</th>
          <th>Term changed</th>
          <th>Reason for change</th>
          <th>Changed by</th>
          <th>&nbsp;</th>
        </tr>
        <?php
          foreach ($model->changes as $uri) {

            $terms = $model->graph->get_subjects_where_resource('http://www.w3.org/2004/02/skos/core#changeNote', $uri);

            echo '<tr class="term">';
            $date = $model->graph->get_first_literal($uri, DC_DATE);
            echo '<td valign="top"><span class="date">' . htmlspecialchars($date) . '</span></td>';
            echo '<td valign="top"><span class="label"><a href="' . htmlspecialchars(local_link($terms[0])) . '">'.  htmlspecialchars($model->graph->get_label($terms[0])) . '</a></span></td>';
            $comment = $model->graph->get_first_literal($uri, RDFS_COMMENT);
            echo '<td valign="top"><span class="comment">' . htmlspecialchars($comment). '</span></td>';
            echo '<td valign="top"><span class="creator"></span></td>';
            echo '<td valign="top"><span class="label"><a href="' . htmlspecialchars(local_link($uri)) . '">more details</a></span></td>';
            echo '</tr>';
          }

          //printf("<pre>%s</pre>", htmlspecialchars($model->graph->to_turtle()));

        ?>
      </table>
      </div>

      <hr>
      <div id="footer" class="quiet append-bottom">
        <?php echo config_item('footer_html'); ?>
      </div>
    </div>
  </body>
</html>

