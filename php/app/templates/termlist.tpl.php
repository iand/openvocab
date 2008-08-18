<?php
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
      echo '<div class="uri">' . htmlspecialchars($item['http://purl.org/rss/1.0/link'][0]) . '</div>';
      if ( isset($item[RDFS_COMMENT])) {
        echo '<div class="comment">' . htmlspecialchars($item[RDFS_COMMENT][0]) . '</div>';
      }
      echo '</p>';
    }
  }
  else {
    echo '<p class="results-info">No matching results.</p>';
  }
}

?>
