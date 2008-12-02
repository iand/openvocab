<?php
if (isset($terms)) {
  echo '<h1>Terms</h1>';
  if ( count($terms) > 0 ) { 
    echo '<p class="note">The following classes and properties have been defined as part of the OpenVocab schema</p>';
    foreach ( $terms as $term) {
      if ( isset($term['term'])) {
        echo '<p class="result">';
        if (isset($term['label'])) {
          echo '<div class="title"><a href="' . remote_to_local($term['term']['value']) . '">' . htmlspecialchars($term['label']['value']) . '</a></div>';
        }
        else {
          echo '<div class="title"><a href="' . remote_to_local($term['term']['value']) . '">' . make_qname($term['term']['value']) . '</a></div>';
        }
        if ( isset($term['comment'])) {
          echo '<div class="comment">' . htmlspecialchars($term['comment']['value']) . '</div>';
        }
        echo '<div class="uri">' . htmlspecialchars($term['term']['value']) . '</div>';
        echo '</p>';
      }
    }
  }
  else {
    echo '<p class="results-info">No terms defined yet.</p>';
  }
}
if (isset($results)) {
  echo '<h1>Matching Terms</h1>';
  if ( $results->total_results > 0 ) { 
    echo '<p class="results-info">Found ' . $results->total_results . ' matching result';
    if ( $results->total_results > 1 ) { 
      echo 's'; 
    }
    echo '.</p>';
    foreach ( $results->items as $item) {
      echo '<p class="result">';
      if ( isset($item[RDFS_LABEL])) {
        echo '<div class="title"><a href="' . remote_to_local($item['http://purl.org/rss/1.0/link'][0]) . '">' . htmlspecialchars($item[RDFS_LABEL][0]) . '</a></div>';
      }
      else {
        echo '<div class="title"><a href="' . remote_to_local($item['http://purl.org/rss/1.0/link'][0]) . '">[unlabelled term]</a></div>';
      }
      if ( isset($item[RDFS_COMMENT])) {
        echo '<div class="comment">' . htmlspecialchars($item[RDFS_COMMENT][0]) . '</div>';
      }
      echo '<div class="uri">' . htmlspecialchars($item['http://purl.org/rss/1.0/link'][0]) . '</div>';
      echo '</p>';
    }
  }
  else {
    echo '<p class="results-info">No matching results.</p>';
  }
}

?>
<p>This page in other formats: <a href="<?php echo(htmlspecialchars(remote_to_local(VOCAB_SCHEMA))); ?>.rdf">RDF/XML</a>, <a href="<?php echo(htmlspecialchars(remote_to_local(VOCAB_SCHEMA))); ?>.turtle">Turtle</a>, <a href="<?php echo(htmlspecialchars(remote_to_local(VOCAB_SCHEMA))); ?>.json">RDF/JSON</a></p>
