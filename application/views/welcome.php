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
          <p>OpenVocab is a community maintained vocabulary intended for use on the Semantic Web, ideal for properties and classes that don't warrant the effort of creating or maintaining a full schema. OpenVocab allows anyone to create and modify vocabulary terms using their web browser.</p>


      <?php
        if (isset($property_count) && isset($class_count)) {
          printf('<p>OpenVocab defines %s propert%s and %s class%s. ', $property_count, $property_count == 1 ? 'y' : 'ies', $class_count, $class_count == 1 ? '' : 'es');
          if (isset($new_terms)) {
            print('Recent additions include ');
            for ($i = 0; $i < count($new_terms); $i++) {
              $term_info = $new_terms[$i];
              if ($i > 0) {
                if ($i == count($new_terms) - 1) {
                  echo ' and ';
                }
                else {
                  echo ', ';
                }
              }
              printf('<a href="%s">%s</a>', htmlspecialchars(local_link($term_info['uri'])), htmlspecialchars($term_info['label']));
            }
          }
          print('</p>');
        }
        ?>
        <?php

        if (isset($recent_changes)) {
          print "<h2>Recent Activity</h2><ul>\n";
          foreach ($recent_changes as $change_info) {
            printf('<li><span class="date">%s</span>: <a href="%s">%s</a> was %s by %s <a href="%s" class="details">details</a></li>' . "\n", htmlspecialchars($change_info['notedate']), htmlspecialchars(local_link($change_info['uri'])), htmlspecialchars($change_info['label']), htmlspecialchars(strtolower($change_info['notelabel'])), htmlspecialchars($change_info['openid']), htmlspecialchars(local_link($change_info['note'])));
          }

          print '</ul><p><a href="/changes">All changes</a></p>' . "\n";
        }

      ?>

    </div>
      <div class="span-8 last">
        <div class="box">
          <h2>News</h2>
          <p>This is a test deployment of OpenVocab version 2. Please <a href="http://code.google.com/p/openvocab/issues/list">report errors and comments on this application</a>.</p>
          <p>Read the <a href="http://blog.iandavis.com/2008/12/introducing-openvocab">blog post</a> introducing OpenVocab.</p>

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




