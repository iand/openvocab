<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN"
   "http://www.w3.org/TR/html4/strict.dtd">

<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">

    <?php
      $title = $model->get_qname() . ', a ';
      $title .= $model->is_property() ? 'property' : 'class';
      $title .= ' in the '. config_item('vocab_name') . ' RDF schema';

      echo '<title>Delete ' . htmlspecialchars($title) . '</title>' . "\n";
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
    <h2 class="bottom">Confirm Deletion of <?php echo htmlspecialchars($model->label . ' (' . $model->get_qname() . ')') ; ?></h2>
    <hr>
      <div class="span-24 last">
      <?php if (isset($msg)) { echo "<div class=\"alert\">$msg</div>"; } ?>
      <?php if (isset($error)) { echo "<div class=\"error\">$error</div>"; } ?>
      <?php if (isset($success)) { echo "<div class=\"success\">$success</div>"; } ?>
        <p><strong>Full URI:</strong> <a href="<?= htmlspecialchars($model->get_uri()); ?>"><?= htmlspecialchars($model->get_uri()); ?></a></p>

        <form action="/forms/deleteterm" method="POST">
          <p>
            <label for="reason">Enter a reason for deletion: </label>
            <input type="text" name="reason" id="reason" value="Removed due to spam"/>

          </p>
          <p>
            <input type="hidden" name="confirm" id="confirm" value="1"/>
            <input type="hidden" name="uri" id="uri" value="<?php echo htmlspecialchars($model->get_uri());?>"/>
            <input type="submit" name="action" value="Confirm Deletion" />
            or <a href="<?php echo(htmlspecialchars(local_link($model->userdocs))); ?>">Cancel</a>
          </p>

        </form>

      </div>
      <hr>
      <div id="footer" class="quiet append-bottom">
        <?php echo config_item('footer_html'); ?>
      </div>
    </div>
  </body>
</html>

