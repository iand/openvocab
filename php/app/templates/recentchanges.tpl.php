<h1>Recent Changes</h1>

<?php 
  if (count($history) > 0) {
    echo '<ul>';
    foreach ($history as $item) {
      echo '<li>';
      $date = strtotime($item['date']['value']);
      echo '<a href="' . remote_to_local(htmlspecialchars($item['soc']['value'])) . '">';
      echo htmlspecialchars($item['title']['value']) . ': ';
      echo '</a>';
      echo '<span class="date">' . htmlspecialchars(strftime("%R, %e %B %Y", $date)) . '</span> by ' . htmlspecialchars($item['creator']['value']);
      echo ' (<em>' . htmlspecialchars($item['reason']['value'])  . '</em>)';
      echo '</li>';
    }
    echo '</ul>';
  }
?>
