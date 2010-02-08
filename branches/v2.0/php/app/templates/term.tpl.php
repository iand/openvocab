<?php 
  require_once MORIARTY_DIR . 'moriarty.inc.php';
  require_once OV_LIB_DIR . 'markdown/markdown.php';
  $parser = new Markdown_Parser();

  $index = $this->description->get_index();

  $is_property = TRUE;  
  $prefix = "Property";
  if ( $this->description->has_resource_triple( $this->uri, RDF_TYPE, OWL_CLASS ) ) {
    $is_property = FALSE;
    $prefix = "Class";
  }
  $this->document->page_title = 'OpenVocab ' . $prefix . ': ' . $this->description->get_first_literal($this->uri, RDFS_LABEL, '[unlabelled term]');
  $this->document->alternates = array();
  $this->document->alternates['application/rdf+xml'] = $this->uri . '/rdf';
  $this->document->alternates['application/json'] = $this->uri . '/json';
  $this->document->alternates['application/x-turtle'] = $this->uri . '/turtle';

?>



<h1><?php echo $prefix; ?>: <?php echo htmlspecialchars($this->description->get_first_literal($this->uri, RDFS_LABEL, '[unlabelled term]')); ?> (ov:<?php echo htmlspecialchars($this->name); ?>)</h1>
<div class="terminfo">
<p><strong>Full URI</strong>: <a href="<?php echo htmlspecialchars(remote_to_local($this->uri)); ?>" class="uri"><?php echo htmlspecialchars($this->uri); ?></a></p>

<?php if ( $this->description->subject_has_property( $this->uri, RDFS_COMMENT ) ) { ?>
<p class="comment"><?php echo htmlspecialchars($this->description->get_first_literal($this->uri, RDFS_COMMENT, 'No available comment')); ?></p>
<?php } ?>

<?php if ( $this->description->subject_has_property( $this->uri, SP_MARKDOWNDESCRIPTION ) ) { ?>
<p class="description"><?php echo $parser->transform($this->description->get_first_literal($this->uri, SP_MARKDOWNDESCRIPTION, 'No available description')); ?></p>
<?php } ?>

<?php
  if ($is_property) {
    $characteristics = array();
    
    if ( $this->description->has_resource_triple( $this->uri, RDF_TYPE, OWL_SYMMETRICPROPERTY ) ) {
      $characteristics[] = 'symmetrical';
    }
    if ( $this->description->has_resource_triple( $this->uri, RDF_TYPE, OWL_TRANSITIVEPROPERTY ) ) {
      $characteristics[] = 'transitive';
    }
    if ( $this->description->has_resource_triple( $this->uri, RDF_TYPE, OWL_FUNCTIONALPROPERTY ) ) {
      $characteristics[] = 'functional';
    }
    if ( $this->description->has_resource_triple( $this->uri, RDF_TYPE, OWL_INVERSEFUNCTIONALPROPERTY ) ) {
      $characteristics[] = 'inverse functional';
    }
  
      
    if ( count($characteristics) > 0 ) {
      echo '<h2>Characteristics</h2>';      
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
    
    if ( $this->description->subject_has_property($this->uri, RDFS_DOMAIN) || $this->description->subject_has_property($this->uri, RDFS_RANGE ) ) {
      echo '<h2>Class Information</h2>';
      list_relations_prose($index, $this->uri, RDFS_DOMAIN, 'Having this property implies being ');
      list_relations_prose($index, $this->uri, RDFS_RANGE, 'Every value of this property is ');
    }
    if (      $this->description->subject_has_property($this->uri, RDFS_SUBPROPERTYOF) 
          ||  $this->description->subject_has_property($this->uri, OWL_EQUIVALENTPROPERTY ) 
          ||  $this->description->subject_has_property($this->uri, OWL_INVERSEOF ) 
        
        ) {
      echo '<h2>Relation to Other Properties</h2>';
      list_relations($index, $this->uri, RDFS_SUBPROPERTYOF, 'Sub-property of');
      list_relations($index, $this->uri, OWL_EQUIVALENTPROPERTY, 'Equivalent to');
      list_relations($index, $this->uri, OWL_INVERSEOF, 'Inverse of');
    }
  }
  else {
    if ( $this->description->subject_has_property($this->uri, RDFS_SUBCLASSOF) || 
          $this->description->subject_has_property($this->uri, OWL_DISJOINTWITH)
        ) {
      echo '<h2>Class Information</h2>';
      list_relations_prose($index, $this->uri, RDFS_SUBCLASSOF, 'Being a member of this class implies also being a member of ', false);
      list_relations_prose($index, $this->uri, OWL_DISJOINTWITH, 'No member of this class can also be a member of ', false);
      
    }

    if ( $this->description->subject_has_property($this->uri, OWL_EQUIVALENTCLASS) ) {
      echo '<h2>Relation to Other Classes</h2>';
      list_relations($index, $this->uri, OWL_EQUIVALENTCLASS, 'Equivalent to');
    }
  }

  echo '</div>';



    echo '<div class="box">';
    if ($is_property) {
      ?>
    <div><a href="/forms/editprop?uri=<?php echo htmlspecialchars(urlencode($this->uri));?>" accesskey="e">Edit</a> | <a href="/forms/newprop?subPropertyOf=<?php echo htmlspecialchars(urlencode($this->uri));?>">Add sub-property</a></div>
      <?php
      }
      else {
    ?>
    <div><a href="/forms/editclass?uri=<?php echo htmlspecialchars(urlencode($this->uri));?>" accesskey="e">Edit</a> | <a href="/forms/newclass?subClassOf=<?php echo htmlspecialchars(urlencode($this->uri));?>">Add sub-class</a></div>
    <?php
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
      <p>This term is considered to be <strong><?php echo htmlspecialchars($this->description->get_first_literal($this->uri, 'http://www.w3.org/2003/06/sw-vocab-status/ns#term_status', '[unknown]')); ?></strong>. Unstable terms may be edited at any time by anyone so their meaning may change unpredictably.</p>
    </div>


<h2 style="clear:both;">RDF</h2>
<pre><?php echo htmlspecialchars($this->description->to_turtle()); ?></pre>
<p>This description in other formats: <a href="<?php echo(htmlspecialchars(remote_to_local($this->uri))); ?>.rdf">RDF/XML</a>, <a href="<?php echo(htmlspecialchars(remote_to_local($this->uri))); ?>.turtle">Turtle</a>, <a href="<?php echo(htmlspecialchars(remote_to_local($this->uri))); ?>.json">RDF/JSON</a></p>
