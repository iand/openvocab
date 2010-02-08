<script lang="text/javascript">
   // When the page is ready

    function create_textbox(event) {
      last_textbox = $(this).prevAll('input.text:first');
      last_id = last_textbox.attr('id');
      suffix = last_id.substr($(this).attr('id').indexOf('_')+1);
      prefix = last_id.substr(0,$(this).attr('id').indexOf('_'));
      new_id = prefix + '_' + ++suffix;
      new_textbox = last_textbox.clone();
      new_textbox.attr('id', new_id);
      
      $(this).before('<br />').before(new_textbox);
      if (prefix == 'domain' || prefix == 'range') {
        $("#" + new_id).autocomplete(classes, autocomplete_config);
      }
      else {
        $("#" + new_id).autocomplete(properties, autocomplete_config);
      }
      // Stop the link click from doing its normal thing
      return false;
    }

    

    var classes = [
 <?php
      if (isset($classes)) {
        foreach ($classes as $class_info) {
          echo '      { label: "' . htmlspecialchars($class_info['label']) . '", uri: "' . htmlspecialchars($class_info['uri']) . '" },' . "\n";
        }
      }
 ?>
      { label: "foaf:Agent", uri: "http://xmlns.com/foaf/0.1/Agent" },
      { label: "foaf:Document", uri: "http://xmlns.com/foaf/0.1/Document" },
      { label: "foaf:Person", uri: "http://xmlns.com/foaf/0.1/Person" },
      { label: "foaf:Image", uri: "http://xmlns.com/foaf/0.1/Image" },
      { label: "foaf:OnlineAccount", uri: "http://xmlns.com/foaf/0.1/OnlineAccount" },
      { label: "foaf:Organization", uri: "http://xmlns.com/foaf/0.1/Organization" },
      { label: "foaf:Project", uri: "http://xmlns.com/foaf/0.1/Project" },
      { label: "foaf:Group", uri: "http://xmlns.com/foaf/0.1/Group" },
      { label: "rdfs:Literal", uri: "http://www.w3.org/2000/01/rdf-schema#Literal" },
      { label: "geo:SpatialThing", uri: "http://www.w3.org/2003/01/geo/wgs84_pos#SpatialThing" },
      { label: "sioc:Community", uri: "http://rdfs.org/sioc/ns#Community" },
      { label: "sioc:Forum", uri: "http://rdfs.org/sioc/ns#Forum" },
      { label: "sioc:Item", uri: "http://rdfs.org/sioc/ns#Item" },
      { label: "sioc:Post", uri: "http://rdfs.org/sioc/ns#Post" },
      { label: "sioc:Site", uri: "http://rdfs.org/sioc/ns#Site" },
      { label: "sioc:User", uri: "http://rdfs.org/sioc/ns#User" },
      { label: "skos:Concept", uri: "http://www.w3.org/2008/05/skos#Concept" },
      { label: "bio:Event", uri: "http://purl.org/vocab/bio/0.1/Event" },
      { label: "doap:Project", uri: "http://usefulinc.com/ns/doap#Project" },
    ];

    var properties = [
 <?php
      if (isset($properties)) {
        foreach ($properties as $prop_info) {
          echo '      { label: "' . htmlspecialchars($prop_info['label']) . '", uri: "' . htmlspecialchars($prop_info['uri']) . '" },' . "\n";
        }
      }
 ?>
      { label: "foaf:knows", uri: "http://xmlns.com/foaf/0.1/knows" },

      { label: "rdfs:label", uri: "http://www.w3.org/2000/01/rdf-schema#label" },
      { label: "rdfs:comment", uri: "http://www.w3.org/2000/01/rdf-schema#comment" },
      { label: "rdfs:isDefinedBy", uri: "http://www.w3.org/2000/01/rdf-schema#isDefinedBy" },
      { label: "rdfs:seeAlso", uri: "http://www.w3.org/2000/01/rdf-schema#seeAlso" },

      { label: "foaf:isPrimaryTopicOf", uri: "http://xmlns.com/foaf/0.1/isPrimaryTopicOf" },
      { label: "foaf:nick", uri: "http://xmlns.com/foaf/0.1/nick" },
      { label: "foaf:name", uri: "http://xmlns.com/foaf/0.1/name" },
      { label: "foaf:primaryTopic", uri: "http://xmlns.com/foaf/0.1/primaryTopic" },
      { label: "foaf:topic", uri: "http://xmlns.com/foaf/0.1/topic" },
      { label: "foaf:page", uri: "http://xmlns.com/foaf/0.1/page" },
      { label: "foaf:img", uri: "http://xmlns.com/foaf/0.1/img" },
      { label: "foaf:depiction", uri: "http://xmlns.com/foaf/0.1/depiction" },
      { label: "foaf:depicts", uri: "http://xmlns.com/foaf/0.1/depicts" },
      { label: "foaf:homepage", uri: "http://xmlns.com/foaf/0.1/homepage" },
      { label: "foaf:weblog", uri: "http://xmlns.com/foaf/0.1/weblog" },
      { label: "foaf:surname", uri: "http://xmlns.com/foaf/0.1/surname" },
      { label: "foaf:givenname", uri: "http://xmlns.com/foaf/0.1/givenname" },
      { label: "foaf:interest", uri: "http://xmlns.com/foaf/0.1/interest" },
      { label: "foaf:made", uri: "http://xmlns.com/foaf/0.1/made" },
      { label: "foaf:maker", uri: "http://xmlns.com/foaf/0.1/maker" },
      { label: "foaf:based_near", uri: "http://xmlns.com/foaf/0.1/based_near" },
      { label: "foaf:member", uri: "http://xmlns.com/foaf/0.1/member" },

      { label: "dc:title", uri: "http://purl.org/dc/elements/1.1/title" },
      { label: "dc:description", uri: "http://purl.org/dc/elements/1.1/description" },
      { label: "dc:creator", uri: "http://purl.org/dc/elements/1.1/creator" },
      { label: "dc:date", uri: "http://purl.org/dc/elements/1.1/date" },
      { label: "dc:rights", uri: "http://purl.org/dc/elements/1.1/rights" },
      { label: "dc:subject", uri: "http://purl.org/dc/elements/1.1/subject" },
      { label: "skos:subject", uri: "http://www.w3.org/2004/02/skos/core#subject" },
      { label: "skos:isSubjectOf", uri: "http://www.w3.org/2004/02/skos/core#isSubjectOf" },
    ];




    var autocomplete_config = {
      minChars: 1,
      width: 400,
      matchContains: true,
      autoFill: false,
      formatItem: function(row, i, max) {
        return row.label + " (" + row.uri + ")";
      },
      formatMatch: function(row, i, max) {
        return row.label + " " + row.uri;
      },
      formatResult: function(row) {
        return row.uri;
      }
    };
    
   $(document).ready(function(){
    
     $(".more").click(create_textbox);
      
      $("#label_en").keyup(function() {
        $(".example .label").html( $(this).val() );
      });
      
     $("#plural_en").keyup(function() {
        $(".example .plural").html( $(this).val() );
      });  




      $("#domain_0").autocomplete(classes, autocomplete_config);
      $("#range_0").autocomplete(classes, autocomplete_config);
      $("#subprop_0").autocomplete(properties, autocomplete_config);
      $("#inverse_0").autocomplete(properties, autocomplete_config);
      $("#equivalent_0").autocomplete(properties, autocomplete_config);


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
        if ( ! $response->is_success() ) {
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
          <td valign="top" class="first"><? echo(VOCAB_NS); ?><input type="text" class="text" name="slug" id="slug" value="<?php echo htmlspecialchars($slug); ?>"/><span class="hint">(Take care: you cannot edit this after you save)</span><br /><span class="hint">Last segment of URI must be mixed case, must start with a lowercase letter, contain only letters, numbers and hyphen</span></td>
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
            <span class="hint">Capitalise this label using title case, as though it was being used as a heading in a table. A useful naming convention for labels is to follow the <a href="http://esw.w3.org/topic/RoleNoun">role-noun</a> pattern. Would your label fit into the following sentences:</span>
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
            <span class="hint example">"the <em class="plural"><?php echo htmlspecialchars($plural_en); ?></em> of thing are foo and bar" or "foo and bar are <em class="plural"><?php echo htmlspecialchars($plural_en); ?></em> of foo"</span> </span>
          
          </td>
        </tr>
                
        <tr>
          <th valign="top"><label for="comment_en">Plain text description <br/>(in English): </label></th>
          <td valign="top"><textarea rows="4" cols="80" name="comment_en" id="comment_en"><?php echo htmlspecialchars($comment_en); ?></textarea></td>
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
          list_form_fields('Domain', 'domain', $domain); 
          list_form_fields('Range', 'range', $range); 
          list_form_fields('Sub-property of', 'subprop', $subprop); 
          list_form_fields('Inverse of', 'inverse', $inverse); 
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
