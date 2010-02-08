<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <title><?php if (isset($this->document->page_title)) { e($this->document->page_title); } else { e("OpenVocab"); }?></title>
    <link rel="stylesheet" type="text/css" href="/styles/screen.css" />
    <link rel="stylesheet" type="text/css" href="/styles/jquery.autocomplete.css" />
    <script type="text/javascript" src="/js/jquery-1.2.4.min.js"></script>          
    <script type='text/javascript' src='/js/jquery.autocomplete.js'></script>
    <script type="text/javascript" src="/js/pretty.js"></script>          
    <script type="text/javascript">                                         
     $(document).ready(function(){
      
       $(".date").each(function() { $(this).html(prettyDate($(this).html())); });
     });
    </script> 
    <?php
      if (isset($this->document->alternates) && is_array($this->document->alternates)) {
        foreach ($this->document->alternates as $type => $uri) {
          echo '<link rel="alternate" type="';
          e($type);
          echo '" href="';
          e($uri);
          echo '" />' . "\n";
        }
      }
      ?>
    
    
    
   </head>

  <body>
    <div id="wrap">
    
      <div id="header">
        <h1><a href="/" title="Home">OpenVocab [alpha]</a></h1>
        <ul id="nav">
        <?php foreach (array_keys($this->map) as $name): ?>
          <li id="n-<?php e($name); ?>"><a href="<?php e($this->url($name)); ?>"><?php e($this->map[$name]); ?></a></li>
        <?php endforeach; ?>
        </ul>
      </div>
      <div id="search">
        <form action="/terms" method="get">
          <label for="q">Find vocabulary terms: </label><input type="text" name="q" id="q" size="40" value="<?php if (isset($_GET['q'])) e($_GET['q'])?>"/> <input type="submit" value="search" />
        </form>
      </div>
    
    
      <div id="content">
        <div class="gutter">
          <?php echo $content; ?>
        </div>
      </div>

      <div id="footer">
        <div class="gutter">
          <p>Service provided by <a href="http://iandavis.com/">Ian Davis</a> using the <a href="http://www.talis.com/platform">Talis Platform</a>. Project hosting by <a href="http://code.google.com/p/openvocab/">Google Code</a><br />
          All text and data are in the <a href="http://open.vocab.org.local/about/rights">Public Domain</a>.
          </p>
        </div>
      </div>

<?php
  if (isset($_GET['debug'])) {
    
    echo '<hr /><p>';
    echo '<strong>STORE_URI</strong>: ' . ( defined('STORE_URI') ? htmlspecialchars(STORE_URI) : '[not defined]') . '<br />';
    echo '<strong>VOCAB_NS</strong>: ' . ( defined('VOCAB_NS') ? htmlspecialchars(VOCAB_NS) : '[not defined]') . '<br />';
    echo '<strong>OV_APP_DIR</strong>: ' . ( defined('OV_APP_DIR') ? htmlspecialchars(OV_APP_DIR) : '[not defined]') . '<br />';
    echo '<strong>OV_LIB_DIR</strong>: ' . ( defined('OV_LIB_DIR') ? htmlspecialchars(OV_LIB_DIR) : '[not defined]') . '<br />';
    echo '<strong>MORIARTY_DIR</strong>: ' . ( defined('MORIARTY_DIR') ? htmlspecialchars(MORIARTY_DIR) : '[not defined]') . '<br />';
    echo '<strong>OV_ARC_DIR</strong>: ' . ( defined('OV_ARC_DIR') ? htmlspecialchars(OV_ARC_DIR) : '[not defined]') . '<br />';
    echo '<strong>OV_KONSTRUKT_DIR</strong>: ' . ( defined('OV_KONSTRUKT_DIR') ? htmlspecialchars(OV_KONSTRUKT_DIR) : '[not defined]') . '<br />';
    echo '<strong>MORIARTY_ARC_DIR</strong>: ' . ( defined('MORIARTY_ARC_DIR') ? htmlspecialchars(MORIARTY_ARC_DIR) : '[not defined]') . '<br />';
    echo '<strong>MORIARTY_OPT_NO_ETAG</strong>: ' . ( defined('MORIARTY_OPT_NO_ETAG') ? htmlspecialchars(MORIARTY_OPT_NO_ETAG) : '[not defined]') . '<br />';
    echo '<strong>MORIARTY_HTTP_CACHE_DIR</strong>: ' . ( defined('MORIARTY_HTTP_CACHE_DIR') ? htmlspecialchars(MORIARTY_HTTP_CACHE_DIR) : '[not defined]') . '<br />';
    echo '<strong>MORIARTY_HTTP_CACHE_USE_STALE_ON_FAILURE</strong>: ' . ( defined('MORIARTY_HTTP_CACHE_USE_STALE_ON_FAILURE') ? htmlspecialchars(MORIARTY_HTTP_CACHE_USE_STALE_ON_FAILURE) : '[not defined]') . '<br />';
    echo '<strong>MORIARTY_HTTP_CACHE_READ_ONLY</strong>: ' . ( defined('MORIARTY_HTTP_CACHE_READ_ONLY') ? htmlspecialchars(MORIARTY_HTTP_CACHE_READ_ONLY) : '[not defined]') . '<br />';
    echo '</p>';
  }
?>

    </div>

  </body>
</html>
