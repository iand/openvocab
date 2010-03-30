<?php echo '<' ?>
?xml version="1.0" encoding="utf-8"?>
<feed xmlns="http://www.w3.org/2005/Atom">

<?php
    $title = $model->get_qname() . ', a ';
    $title .= $model->is_property() ? 'property' : 'class';
    $title .= ' in the '. config_item('vocab_name') . ' RDF schema';

    printf('  <title>%s</title>' . "\n", htmlspecialchars($title));

    foreach ($links as $link) {
      $rel = 'alternate';
      if ($link['type'] == 'application/atom+xml') {
        $rel = 'self';
      }
      printf('  <link rel="%s" type="%s" href="%s" title="%s" />' . "\n", $rel, htmlspecialchars($link['type']), htmlspecialchars($link['href']), htmlspecialchars($link['title']) );
    }

    if (count($model->changes)) {
      $date = $model->graph->get_first_literal($model->changes[0], 'http://purl.org/dc/elements/1.1/created');
      printf('  <updated>%s</updated>' . "\n", htmlspecialchars($date));
    }
    else {
      print '  <updated>2010-03-21T11:02:00Z</updated>' . "\n";
    }
    printf('  <id>%s</id>' . "\n", htmlspecialchars($uri));

    foreach ($model->changes as $change_uri) {
      print('  <entry>' . "\n");
      $label = $model->graph->get_first_literal($change_uri, RDFS_LABEL);
      $comment = $model->graph->get_first_literal($change_uri, RDFS_COMMENT);
      $date = $model->graph->get_first_literal($change_uri, 'http://purl.org/dc/elements/1.1/created');
      $creator_uri = $model->graph->get_first_resource($change_uri, DC_CREATOR);
      $openid = $model->graph->get_first_resource($creator_uri, 'http://xmlns.com/foaf/0.1/openid');

      printf('    <title>%s by %s</title>' . "\n", htmlspecialchars($label), htmlspecialchars($openid));
      printf('    <link href="%s"/>' . "\n", htmlspecialchars($change_uri));
      printf('    <id>%s</id>' . "\n", htmlspecialchars($change_uri));
      printf('    <updated>%s</updated>' . "\n", htmlspecialchars($date));
      printf('    <author><name>%s</name></author>' . "\n", htmlspecialchars($openid));
      printf('    <summary>%s</summary>' . "\n", htmlspecialchars($comment));
      print('  </entry>' . "\n");
    }

  ?>
</feed>
