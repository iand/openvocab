<h1>Recent Changes</h1>

<?php 
  if (count($history) > 0) {
    echo '<p class="result">';
    foreach ($history as $item) {
      echo '<p class="result">';
      echo '<div class="title">';
      $date = strtotime($item['date']['value']);
      echo '<a href="' . remote_to_local(htmlspecialchars($item['soc']['value'])) . '">';
      echo htmlspecialchars($item['title']['value']);
      echo '</a>';
      echo '</div>';
      echo '<div class="comment">' . htmlspecialchars($item['creator']['value']) . ' said "' . htmlspecialchars($item['reason']['value'])  . '" at ' . htmlspecialchars(strftime("%R, %e %B %Y", $date)) ;
      echo '<div class="uri">' . remote_to_local(htmlspecialchars($item['soc']['value'])) . '</div>';
      echo '</p>';
    }
    echo '</p>';
  }
?>
