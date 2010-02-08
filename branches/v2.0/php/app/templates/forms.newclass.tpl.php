 <script lang="text/javascript">
   // When the page is ready

    label_edited = false;
    plural_edited = false;

    function create_textbox(event) {
      last_textbox = $(this).prevAll('input.text:first');
      last_id = last_textbox.attr('id');
      prefix = last_id.substr($(this).attr('id').indexOf('_')+1);
      new_id = last_id.substr(0,$(this).attr('id').indexOf('_')) + '_' + ++prefix;
      new_textbox = last_textbox.clone();
      new_textbox.attr('id', new_id);
      
      $(this).before('<br />').before(new_textbox);

      // Stop the link click from doing its normal thing
      return false;
    }
   $(document).ready(function(){
    
     $(".more").click(create_textbox);
     $("#label_en").keyup(function() {
        label_edited = true;
        prefix = "";
        first_letter = $(this).val().substr(0,1);
        
        if ( first_letter == 'a' || first_letter == 'e' || first_letter == 'i' || first_letter == 'o' || first_letter == 'u') {
          prefix="n";
        }
        $(".hint .label").html( prefix + ' ' + $(this).val() );
      });
     $("#plural_en").keyup(function() {
        $(".hint .plural").html( $(this).val() );
      });      

   
  <?php
    if ($mode == 'new') {
  ?>   
      $("#slug").keyup(function() {
        if (! label_edited ) {
          $("#label_en").val( $(this).val() );
          $("#label_en").keyup();
          label_edited = false;
        }
      });
  <?php
    }
  ?>

   });
 </script>
    <h1><?php echo htmlspecialchars($title); ?></h1>

    <?php
      if (count($messages) > 0) {
    ?>
        <p class="error">Your request could not be sent. The following problems need to be corrected:</p>
        <ul>
    <?php
          foreach ($messages as $message) {
            echo '<li>' . htmlspecialchars($message) . '</li>';
          }
    ?>
        </ul>
    <?php
      }
    ?>

    <?php
      

      if ( $response ) {
        if ( ! $response->is_success()) {
          echo '<p class="error">There was a problem saving your changes: ' . htmlspecialchars($response->body) . '</p>';

          echo '<p>The HTTP request sent was:</p>';
          echo '<pre>' . htmlspecialchars($response->request->to_string()) . '</pre>';
          echo '<p>The server response was:</p>';
          echo '<pre>' . htmlspecialchars($response->to_string()) . '</pre>';
        }
      }

    ?>

    <form method="POST" action="">
      <table class="form" cellspacing="0" cellpadding="0" border="0">
        
        <tr>
          <th valign="top" class="first"><label for="slug">URI: </label></th>
          <?php
            if ($mode == 'new') {
          ?>      
          <td valign="top" class="first">http://open.vocab.org/terms/<input type="text" class="text" name="slug" id="slug" value="<?php echo htmlspecialchars($slug); ?>"/><br /><span class="hint">Last segment of URI must be mixed case, must start with an uppercase letter, contain only letters, numbers and hyphen</span></td>
          <?
            } else {
          ?>
            <td valign="top" class="first"><?php echo htmlspecialchars($uri); ?><input type="hidden" name="uri" id="uri" value="<?php echo htmlspecialchars($uri); ?>" /></td>
          <?php
            }
          ?>

        </tr>

        <tr>
          <th valign="top"><label for="label_en">Singular label (in English): </label></th>
          <td valign="top"><input type="text" class="text" size="60" name="label_en" id="label_en" value="<?php echo htmlspecialchars($label_en); ?>"/>
            <br />
            <span class="hint">Capitalise this label using title case, as though it was being used as a heading in a table. Would your singular label fit into the following sentence: <span class="example">"foo is a<em class="label"><?php echo " " . htmlspecialchars($label_en); ?></em>"</span></span>
          </td>
        </tr>
        <tr>
          <th valign="top"><label for="plural_en">Plural label (in English): </label></th>
          <td valign="top"><input type="text" class="text" size="60" name="plural_en" id="plural_en" value="<?php echo htmlspecialchars($plural_en); ?>"/>
            <br />
            <span class="hint">Would your plural label fit into the following sentence: <span class="example">"foo is one of a number of <em class="plural"><?php echo " " . htmlspecialchars($plural_en); ?></em>"</span></span>
          </td>
        </tr>        
        
        <tr>
          <th valign="top"><label for="comment_en">Plain text description <br/>(in English): </label></th>
          <td valign="top"><textarea rows="4" cols="80" name="comment_en" id="comment_en"><?php echo htmlspecialchars($comment_en); ?></textarea></td>
        </tr>

        <?php 
          list_form_fields('Sub-class of', 'subclass', $subclass); 
          list_form_fields('Disjoint with', 'disjoint', $disjoint); 
          list_form_fields('Equivalent to', 'equivalent', $equivalent); 
        ?>
        <tr>
          <th valign="top"><label for="description_en">Notes on usage:<br/>(in English): </label></th>
          <td valign="top"><textarea rows="10" cols="80" name="description_en" id="description_en"><?php echo htmlspecialchars($description_en); ?></textarea><br /><span class="hint">You can use <a href="http://daringfireball.net/projects/markdown/syntax">markdown syntax</a> to format this description</span></td>
        </tr>
      </table>


          <?php
            if ($mode == 'new') {
          ?>  
            <input type="hidden" name="reason" value="Created" />
          
          <?php
          }
          else {
            ?>
          <p>Please enter a reason for this change:</p>
      <table class="form" cellspacing="0" cellpadding="0" border="0">
        <tr>
          <th valign="top"><label for="reason">Reason for change: </label></th>
          <td valign="top"><input type="text" class="text" size="60" name="reason" id="reason" value="<?php echo htmlspecialchars($reason); ?>"/></td>
        </tr>          
      </table>

          <?php
          }
          ?>

      <p class="legal">
        By clicking the save button you are agreeing that the information you have entered into this form should be gifted to the Public Domain
      </p>
      <p class="actions">
        <input type="submit" name="action" value="Save" accesskey="s"/> or <a href="./forms">cancel</a>
      </p>

    </form>
