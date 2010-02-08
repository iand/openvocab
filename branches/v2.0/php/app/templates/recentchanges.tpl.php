<h1>Recent Changes</h1>

<?php 
  if (count($history) > 0) {

    $items = array();
    foreach ($history as $item) {
      $date = strftime("%e %B %Y", strtotime($item['date']['value']));
      if (! array_key_exists($date, $items)) {
        $items[$date] = array();  
      }
      $items[$date][] = $item;
    }

    echo '<p class="result">';
    foreach ($items as $group => $grouped_items) {
      echo '<h2>' . htmlspecialchars($group) . '</h2>';
      foreach ($grouped_items as $item) {
        echo '<p class="result">';
        echo '<div class="title">';
        $date = strtotime($item['date']['value']);
        echo '<a href="' . remote_to_local(htmlspecialchars($item['soc']['value'])) . '">';
        echo htmlspecialchars($item['title']['value']);
        echo '</a>';
        echo '</div>';
        echo '<div class="comment">' . htmlspecialchars($item['creator']['value']) . ' said "' . htmlspecialchars($item['reason']['value'])  . '" at ' . htmlspecialchars(strftime("%R", $date)) ;
        echo '<div class="uri">' . remote_to_local(htmlspecialchars($item['soc']['value'])) . '</div>';
        echo '</p>';
      }
    }
    echo '</p>';
  }
?>
