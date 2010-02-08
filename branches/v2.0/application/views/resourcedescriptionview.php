<?php
require_once MORIARTY_DIR . 'moriarty.inc.php';
$this->load->helper('text');
function e($data) {
  echo(htmlspecialchars($data));
}

?>
<!DOCTYPE HTML SYSTEM>
<html>
  <head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8">
    <meta name="description" content="<?php if (isset($description))  {e($description); }?>">

    <link rel="stylesheet" href="/css/bp/screen.css" type="text/css" media="screen, projection">
    <link rel="stylesheet" href="/css/bp/print.css" type="text/css" media="print">
    <!--[if lt IE 8]><link rel="stylesheet" href="/css/bp/ie.css" type="text/css" media="screen, projection"><![endif]-->

    <link rel="stylesheet" href="/css/screen.css" type="text/css" media="screen">
    <!--<script src="/js/jquery-1.3.2.min.js" type="text/javascript"></script> -->
    <script src="/js/moriarty.js" type="text/javascript"></script>
    <title>Semantic Library<?php if (isset($title)) { e(' : ' . $title); } ?></title>
<?php
  if (isset($links)) {
    foreach ($links as $link) {
      echo '    <link rel="alternate" type="' . htmlspecialchars($link['type']) . '" href="' . htmlspecialchars($link['href']) . '" title="' . htmlspecialchars($link['title']) . '">' . "\n";
    }
  }
?>        
      <?php if(isset($js)) echo $js; ?>
    </head>
    <body>
      <div class="container">    
    
    
        <div id="header" class="span-24 last">
        <h1><a href="http://www.talis.com/"><img src="/images/talis-logo-on-bg-lg.png"></a> <a href="/"><img  id="logo" src="/images/logo.png" alt="Semantic Library" width="295" height="38"></a></h1>
        </div>
        <hr class="space">
        <h1 id="title" class="span-22 prepend-1 last">
          <?php 
            if (isset($title)) {
              echo(character_limiter(htmlspecialchars($title), 34));
            }
            else if (isset($title_encoded)) {
              echo($title_encoded);
            }
          ?>
        </h1>
  
        <div id="content" class="span-14 prepend-1 colborder">
          <?php if (isset($content)) { echo $content; } ?>
          
          <div id="topsubjects"></div>
        </div>
        
        <div id="related" class="span-7 last">
          <?php if (isset($sidebar)) { echo ($sidebar); } ?>          
        </div>

        

      <div id="footer" class="span-22 prepend-1 append-1 last">
        <p>
          This service is provided by <a href="http://www.talis.com/">Talis</a> using the <a href="http://www.talis.com/platform">Talis Platform</a>.  
          All data and content is in the public domain and may be used for any purpose without restriction.
          <?php if (isset($resource_uri)) { ?>
          Report a problem with this data using the <a href="http://bugs.semanticweb.org/report?url=<?php echo(htmlspecialchars(urlencode($resource_uri)));?>">semantic web bugtracker</a>.
          <?php } ?>
        </p>
        <p><small>Page rendered in {elapsed_time} seconds</small></p>
      </div>

    </div>

  </body>
</html>
