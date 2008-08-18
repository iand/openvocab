<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <title><?php if (isset($this->document->page_title)) { e($this->document->page_title); } else { e("OpenVocab"); }?></title>
    <link rel="stylesheet" href="/styles/screen.css" />
    <script type="text/javascript" src="/js/jquery-1.2.4.min.js"></script>          
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
    </div>

  </body>
</html>
