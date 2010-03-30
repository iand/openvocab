<?php
function list_form_fields($label, $name, $values, $max) {
  $count = count($values);
  echo '<tr>' . "\n";
  echo '  <th valign="top"><label for="' . $name . '_' . $count . '">' . htmlspecialchars($label) . ': </label></th>' . "\n";
  echo '  <td valign="top" colspan="2">' . "\n";

  for ($i = 0; $i < $count; $i++) {
    $id = $name . '_' . $i;
    echo '      <input type="text" class="text" size="60" name="' . $id . '" id="' . $id . '" value="' . htmlspecialchars($values[$i]) . '"/><br />' . "\n";
  }
  if ($count < $max) {
    echo '    <input type="text" class="text" size="60" name="' . $name . '_' . $count . '" id="' . $name . '_' . $count . '" value=""/>' . "\n";
    echo ' <a href="#" id="' . $name . '_more" class="more">add another</a>' . "\n";
  }
  echo '  </td>' . "\n";
  echo '</tr>' . "\n";
}






?><!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN"
   "http://www.w3.org/TR/html4/strict.dtd">

<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title><?= $page_title ?></title>
    <meta name="description" content="Anyone can participate in creating the <?= htmlspecialchars(config_item('vocab_name')); ?> schema" />

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
      <h2 class="bottom"><?= $page_title ?></h2>
      <hr>
      <div class="span-24">
      <?php if (isset($msg)) { echo "<div class=\"alert\">$msg</div>"; } ?>
      <?php if (isset($error)) { echo "<div class=\"error\">$error</div>"; } ?>
      <?php if (isset($success)) { echo "<div class=\"success\">$success</div>"; } ?>

    <form method="POST" action="<? e($form_action); ?>">
      <table class="form" cellspacing="0" cellpadding="0" border="0">

        <tr>
          <th><label for="slug">URI: </label></th>
          <?php if ($show_slug) { ?>
          <td nowrap="nowrap">http://open.vocab.org/terms/<input type="text" class="text slug" name="slug" id="slug" value="<?php echo set_value('slug'); ?>"/></td>
          <td><span class="hint quiet">(Take care: you cannot edit this after you save)</span><br /><span class="hint quiet">Last segment of URI must be mixed case, must start with an uppercase letter, contain only letters, numbers and hyphen</span></td>
          <?php } else { ?>
          <td colspan="2"><?php echo htmlspecialchars($uri); ?><input type="hidden" name="uri" id="uri" value="<?php echo htmlspecialchars($uri); ?>"/></td>
          <?php } ?>
        </tr>

        <tr>
          <th><label for="label_en">Singular label<br />(in English): </label></th>
          <td><input type="text" class="text" size="60" name="label_en" id="label_en" value="<?php e($term->label); ?>"/></td>
          <td>
            <span class="hint quiet">Capitalise this label using title case, as though it was being used as a heading in a table. Would your singular label fit into the following sentence:</span>
            <br />
            <span class="hint example quiet">"foo is a <em class="label"></em>"</span>
          </td>
        </tr>

        <tr>
          <th><label for="plural_en">Plural label<br />(in English): </label></th>
          <td><input type="text" class="text" size="60" name="plural_en" id="plural_en" value="<?php e($term->plural); ?>"/></td>
          <td>
            <span class="hint quiet">Would your plural label fit into the following sentence:</span>
            <br />
            <span class="hint example quiet">"foo is one of a number of <em class="plural"></em>"</span>
          </td>
        </tr>

        <tr>
          <th><label for="comment_en">Plain text comment<br/>(in English): </label></th>
          <td colspan="2"><textarea rows="4" cols="80" name="comment_en" id="comment_en" style="width:100%;"><?php e($term->comment); ?></textarea></td>
        </tr>

        <?php
          list_form_fields('Sub-class of', 'subclass', $term->superclasses, $max_relations);
          list_form_fields('Disjoint with', 'disjoint', $term->disjoints, $max_relations);
          list_form_fields('Equivalent to', 'equivalent', $term->equivalentclasses, $max_relations);
        ?>
<?php /*
        <tr>
          <th><label for="description_en">Notes on usage:<br/>(in English): </label></th>
          <td colspan="2"><textarea rows="10" cols="80" name="description_en" id="description_en" style="width:100%;"><?php e($term->description) ?></textarea>
          <br /><span class="hint">You can use <a href="http://daringfireball.net/projects/markdown/syntax">markdown syntax</a> to format this description</span></td>
        </tr>
*/ ?>
      </table>

      <?php if ($show_reason) { ?>
        <label for="description_en">What is the reason for this change? </label><br />
        <textarea rows="3" cols="80" name="reason" id="reason" style="width:100%; height:4em;"><?php e($reason); ?></textarea>
      <?php } else { ?>
      <input type="hidden" name="reason" value="Created" />
      <?php } ?>
      <p class="legal">
        By clicking the save button you are agreeing that the information you have entered into this form should be gifted to the Public Domain
      </p>

      <p class="actions">
        <input type="submit" name="action" value="Save" accesskey="s"/> or <a href="<?php echo $cancel_link; ?>">cancel</a>
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

