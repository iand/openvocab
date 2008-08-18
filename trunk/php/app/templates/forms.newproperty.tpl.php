 <script lang="text/javascript">
   // When the page is ready

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
        $(".example .label").html( $(this).val() );
      });

     $("#plural_en").keyup(function() {
        $(".example .plural").html( $(this).val() );
      });  
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
          <td valign="top" class="first">http://open.vocab.org/terms/<input type="text" class="text" name="slug" id="slug" value="<?php echo htmlspecialchars($slug); ?>"/><br /><span class="hint">Last segment of URI must be mixed case, must start with a lowercase letter, contain only letters, numbers and hyphen</span></td>
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
            <span class="hint">A useful convention for labels is to follow the <a href="http://esw.w3.org/topic/RoleNoun">role-noun</a> pattern. Would your label fit into the following sentences:</span>
            <br />
            <span class="hint example">"foo is <em class="label"><?php echo htmlspecialchars($label_en); ?></em> of thing" or "thing has <em class="label"><?php echo htmlspecialchars($label_en); ?></em> foo"</span> </span>
          
          </td>
        </tr>
        <tr>
          <th valign="top"><label for="plural_en">Plural label (in English): </label></th>
          <td valign="top"><input type="text" class="text" size="60" name="plural_en" id="plural_en" value="<?php echo htmlspecialchars($plural_en); ?>"/>
            <br />
            <span class="hint">Would your plural label fit into the following sentences:</span>
            <br />
            <span class="hint example">"the <em class="plural"><?php echo htmlspecialchars($plural_en); ?></em> of thing are foo and bar" or "foo and bar have <em class="plural"><?php echo htmlspecialchars($plural_en); ?></em> foo"</span> </span>
          
          </td>
        </tr>
                
        <tr>
          <th valign="top"><label for="comment_en">Plain text description <br/>(in English): </label></th>
          <td valign="top"><textarea rows="4" cols="80" name="comment_en" id="comment_en"><?php echo htmlspecialchars($comment_en); ?></textarea></td>
        </tr>
        <tr>
          <th valign="top"><label for="description_en">Formatted description<br/>(in English): </label></th>
          <td valign="top"><textarea rows="10" cols="80" name="description_en" id="description_en"><?php echo htmlspecialchars($description_en); ?></textarea><br /><span class="hint">You can use <a href="http://daringfireball.net/projects/markdown/syntax">markdown syntax</a> to format this description</span></td>
        </tr>
        <tr>
          <th valign="top"><label for="type">Value Type: </label></th>
          <td valign="top"><select name="type" id="type"><option>(any)</option><option>Datatype Property</option><option>Object Property</option></select></td>
        </tr>
        <tr>
          <th valign="top"><label>Characteristics: </label></th>
          <td valign="top">
            <label for="functional"><input type="checkbox" name="functional" id="functional" value="1" <?php if($functional) { echo 'checked="checked"'; } ?>/>Is functional?</label><br />
            <label for="inversefunctional"><input type="checkbox" name="inversefunctional" id="inversefunctional" value="1"<?php if($inversefunctional) { echo 'checked="checked"'; } ?>/>Is inverse functional?</label><br />
            <label for="symmetric"><input type="checkbox" name="symmetric" id="symmetric" value="1"<?php if($symmetric) { echo 'checked="checked"'; } ?>/>Is symmetric?</label><br />
            <label for="transitive"><input type="checkbox" name="transitive" id="transitive" value="1"<?php if($transitive) { echo 'checked="checked"'; } ?>/>Is transitive?</label><br />
          </td>
        </tr>

        <?php 
          list_form_fields('Range', 'range', $range); 
          list_form_fields('Domain', 'domain', $domain); 
          list_form_fields('Sub-property of', 'subprop', $subprop); 
          list_form_fields('Inverse of', 'inverse', $inverse); 
          list_form_fields('Equivalent to', 'equivalent', $equivalent); 
        ?>




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
