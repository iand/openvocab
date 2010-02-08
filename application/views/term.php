<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN"
   "http://www.w3.org/TR/html4/strict.dtd">

<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">

    <?php
      $title = $model->get_qname() . ', a ';
      $title .= $model->is_property() ? 'property' : 'class';
      $title .= ' in the '. config_item('vocab_name') . ' RDF schema';

      echo '<title>' . htmlspecialchars($title) . '</title>' . "\n";

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
    <h2 class="bottom"><?php echo htmlspecialchars($model->label . ' (' . $model->get_qname() . ')') ; ?></h2>
    <hr>
      <div class="span-15 colborder">
        <p><strong>Full URI:</strong> <a href="<?= htmlspecialchars($model->get_uri()); ?>"><?= htmlspecialchars($model->get_uri()); ?></a></p>
        <p>"foo is <?= htmlspecialchars($model->label);?> of thing" or "thing has <?= htmlspecialchars($model->label);?> foo"</p>


      <?php
        if ($model->comment) {
          echo '<p>'. htmlspecialchars($model->comment) . '</p>';
        }

        $characteristics = array();

        if ( $model->is_symmetrical ) {
          $characteristics[] = 'symmetrical';
        }
        if ( $model->is_transitive ) {
          $characteristics[] = 'transitive';
        }
        if ( $model->is_functional ) {
          $characteristics[] = 'functional';
        }
        if ( $model->is_inverse_functional ) {
          $characteristics[] = 'inverse functional';
        }


        if ( count($characteristics) > 0 ) {
          echo '<h3>Characteristics</h3>';
          echo '<p>This property is ';
          for ($i = 0; $i < count($characteristics); $i++) {
            if ( $i > 0 ) {
              if ($i == count($characteristics) - 1) { echo ' and '; }
              else { echo ', '; }
            }
            echo $characteristics[$i];
          }
          echo '</p>';
        }


        if ( $model->domains || $model->ranges || $model->superclasses || $model->disjoints ) {
          echo '<h3>Class Information</h3>';
          echo list_relations($model, $model->domains, 'Everything with this property is ');
          echo list_relations($model, $model->ranges, 'Every value of this property is ');
          echo list_relations($model, $model->superclasses, 'Being a member of this class implies also being a member of ', false);
          echo list_relations($model, $model->disjoints, 'No member of this class can also be a member of ', false);
        }

        if ( $model->superproperties || $model->equivalentproperties || $model->inverses ) {
          echo '<h3>Relation to Other Properties</h3>';
          echo list_relations($model, $model->superproperties, 'Sub-property of', false);
          echo list_relations($model, $model->equivalentproperties, 'Equivalent to', false);
          echo list_relations($model, $model->inverses, 'Inverse of', false);
        }

        if ( $model->equivalentclasses ) {
          echo '<h3>Relation to Other Classes</h3>';
          echo list_relations($model, $model->equivalentclasses, 'Equivalent to', false);
        }



        function list_relations($model, $values, $label, $use_definite_article = true) {
          $h = '';
          if (count($values)) {
            $h .= '<p>' . htmlspecialchars($label) . ' ';
            for ($i = 0; $i < count($values); $i++) {
              if ($i > 0) {
                if ($i < count($values) - 1) { $h .= ', '; }
                else if ($i == count($values) - 1) { $h .= ' and '; }
              }

              if ( $use_definite_article ) {
                $h .= 'a';
                if ( preg_match('/^[aeiou]/', $values[$i]) ) {
                  $h .= 'n';
                }
                $h .= ' ';
              }
              $h .= link_uri($model, $values[$i]);


            }
          }

          return $h;
        }

        function link_uri($model, $uri) {
          if (preg_match('/^https?:\/\//', $uri) ) {
            $qname = $model->graph->uri_to_qname($uri) ;
            if ($qname === null) $qname = $uri;
            return '<a href="' . htmlspecialchars($uri) . '" class="uri">' . htmlspecialchars($qname) . '</a>';
          }
          else {
            return htmlspecialchars($uri);
          }
        }

      ?>


      </div>
      <div class="span-8 last">
      <?php
        echo '<div class="box">';
        if ($this->session->userdata('logged_in') ) {
          echo '<div class="actions">';
          if ($model->is_property()) {
            printf('<a href="/forms/editprop?uri=%s" accesskey="e">Edit</a>', htmlspecialchars(urlencode($uri)) );
//            echo ' | ';
//            printf('<a href="/forms/newprop?subPropertyOf=%s">Add sub-property</a>', htmlspecialchars(urlencode($uri)) );
          }
          else {
            printf('<a href="/forms/editclass?uri=%s" accesskey="e">Edit</a>', htmlspecialchars(urlencode($uri)) );
//            echo ' | ';
//            printf('<a href="/forms/newclass?subClassOf=%s">Add sub-class</a>', htmlspecialchars(urlencode($uri)) );
          }

          if ( in_array($this->session->userdata('openid'), config_item('superusers')) ) {
            echo ' | ';
            printf('<a href="/forms/deleteterm?uri=%s">Delete</a>', htmlspecialchars(urlencode($uri)) );
          }
          echo '</div>';
        }
        else {
          echo '<div class="quiet"><a href="/login">Login to edit this term</a></div>';
        }

      if ( isset($history)) {
        echo '<h2>Change History</h2><ul>';
        foreach ($history as $item) {
          echo '<li class="change">';
          $date = strtotime($item['date']['value']);
          echo '<span class="date">' . htmlspecialchars($item['date']['value']) . '</span> ' . htmlspecialchars($item['creator']['value']) . ' said "' .  htmlspecialchars($item['reason']['value']) . '"';
          echo '</li>';
        }
        echo '</ul>';
      }
    ?>
          <p>This term is considered to be <strong><?php echo htmlspecialchars($model->status); ?></strong>. Unstable terms may be edited at any time by anyone so their meaning may change unpredictably.</p>
        </div>
      </div>
      <hr class="space">
      <h3>RDF</h3>
<?php
  if (isset($links)) {
    echo '<p>Other formats: ';
    foreach ($links as $link) {
      echo '<a type="' . htmlspecialchars($link['type']) . '" href="' . htmlspecialchars($link['href']) . '" title="' . htmlspecialchars($link['title']) . '">' . htmlspecialchars($link['title']) . "</a> ";
    }
    echo '</p>';
  }
?>

      <pre><code><?php echo htmlspecialchars($model->graph->to_turtle()); ?></code></pre>
      <hr>
      <div id="footer" class="quiet append-bottom">
        <?php echo config_item('footer_html'); ?>
      </div>
    </div>
  </body>
</html>

