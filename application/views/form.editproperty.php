<?php
function list_form_fields($label, $name, $count, $max) {
  echo '<tr>' . "\n";
  echo '  <th valign="top"><label for="' . $name . '_' . $count . '">' . htmlspecialchars($label) . ': </label></th>' . "\n";
  echo '  <td valign="top" colspan="2">' . "\n";

  for ($i = 0; $i < $count; $i++) {
    $id = $name . '_' . $i;
    echo '      <input type="text" class="text" size="60" name="' . $id . '" id="' . $id . '" value="' . set_value($id) . '"/> ' . form_error($id) . "<br />\n";
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
    <title>New Property</title>
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
      <h2 class="bottom">New Property</h2>
      <hr>
      <div class="span-24">
      <?php if (isset($msg)) { echo "<div class=\"alert\">$msg</div>"; } ?>
      <?php if (isset($error)) { echo "<div class=\"error\">$error</div>"; } ?>
      <?php if (isset($success)) { echo "<div class=\"success\">$success</div>"; } ?>

    <form method="POST" action="">
      <table class="form" cellspacing="0" cellpadding="0" border="0">

        <tr>
          <th class="first"><label for="slug">URI: </label></th>
          <td class="first">http://open.vocab.org/terms/<input type="text" class="text slug" name="slug" id="slug" value="<?php echo set_value('slug'); ?>"/><?php echo form_error('slug'); ?></td>
          <td><span class="hint quiet">(Take care: you cannot edit this after you save)</span><br /><span class="hint quiet">Last segment of URI must be mixed case, must start with a lowercase letter, contain only letters, numbers and hyphen</span></td>
        </tr>

        <tr>
          <th><label for="label_en">Singular label (in English): </label></th>
          <td><input type="text" class="text" size="60" name="label_en" id="label_en" value="<?php echo set_value('label_en'); ?>"/><?php echo form_error('label_en'); ?></td>
          <td>
            <span class="hint quiet">Capitalise this label using title case, as though it was being used as a heading in a table. A useful naming convention for labels is to follow the <a href="http://esw.w3.org/topic/RoleNoun">role-noun</a> pattern. Would your label fit into the following sentences:</span>
            <br />
            <span class="hint example quiet">"foo is <em class="label"></em> of thing" or "thing has <em class="label"></em> foo"</span>
          </td>
        </tr>

        <tr>
          <th><label for="plural_en">Plural label (in English): </label></th>
          <td><input type="text" class="text" size="60" name="plural_en" id="plural_en" value="<?php echo set_value('plural_en'); ?>"/><?php echo form_error('plural_en'); ?></td>
          <td>
            <span class="hint quiet">Would your plural label fit into the following sentences:</span>
            <br />
            <span class="hint example quiet">"the <em class="plural"></em> of thing are foo and bar" or "foo and bar are <em class="plural"></em> of foo"</span>
          </td>
        </tr>

        <tr>
          <th><label for="comment_en">Plain text description <br/>(in English): </label></th>
          <td colspan="2"><textarea rows="4" cols="80" name="comment_en" id="comment_en" style="width:100%;"><?php echo set_value('comment_en'); ?></textarea><?php echo form_error('comment_en'); ?></td>
        </tr>

        <tr>
          <th><label for="type">Value Type: </label></th>
          <td colspan="2"><select name="type" id="type"><option>(any)</option><option>Datatype Property</option><option>Object Property</option></select></td>
        </tr>

        <tr>
          <th><label>Characteristics: </label></th>
          <td colspan="2">
            <label for="functional"><input type="checkbox" name="functional" id="functional" value="1" <?php echo set_checkbox('functional', '1'); ?>/>Is functional?</label><br />
            <label for="inversefunctional"><input type="checkbox" name="inversefunctional" id="inversefunctional" value="1" <?php echo set_checkbox('inversefunctional', '1'); ?>/>Is inverse functional?</label><br />
            <label for="symmetric"><input type="checkbox" name="symmetric" id="symmetric" value="1" <?php echo set_checkbox('symmetric', '1'); ?>/>Is symmetric?</label><br />
            <label for="transitive"><input type="checkbox" name="transitive" id="transitive" value="1" <?php echo set_checkbox('transitive', '1'); ?>/>Is transitive?</label><br />
          </td>
        </tr>

        <?php
          list_form_fields('Domain', 'domain', $domain_count, $max_relations);
          list_form_fields('Range', 'range', $range_count, $max_relations);
          list_form_fields('Sub-property of', 'subprop', $subprop_count, $max_relations);
          list_form_fields('Inverse of', 'inverse', $inverse_count, $max_relations);
          list_form_fields('Equivalent to', 'equivalent', $equivalent_count, $max_relations);
        ?>

        <tr>
          <th><label for="description_en">Notes on usage:<br/>(in English): </label></th>
          <td colspan="2"><textarea rows="10" cols="80" name="description_en" id="description_en" style="width:100%;"><?php echo set_value('description_en'); ?></textarea>
          <br /><span class="hint">You can use <a href="http://daringfireball.net/projects/markdown/syntax">markdown syntax</a> to format this description</span></td>
        </tr>
      </table>

      <input type="hidden" name="reason" value="Created" />

      <p class="legal">
        By clicking the save button you are agreeing that the information you have entered into this form should be gifted to the Public Domain
      </p>

      <p class="actions">
        <input type="submit" name="action" value="Save" accesskey="s"/> or <a href="/create">cancel</a>
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

