<?php
$dark = "#5ABF5B";
$standard = "background-color: #5ABF5B; color: #FFFFFF;";
$hilight = "background-color: #77E178; color: #FFFFFF;";
$pale = "color: black; background: #F6FFF6;";
$borders = "#5ABF5B";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <title><?php if (isset($this->document->page_title)) { echo($this->document->page_title); } else { echo("OpenVocab"); }?></title>
    <link rel="stylesheet" href="/css/screen.css" />
    <style type="text/css">
body {  margin: 0; padding:0 ; font-family: Georgia,serif; font-size: small; background:#eee url(/images/bg.gif) fixed;}
a { color: #336699; text-decoration: none; }
div.error { margin: 20px; padding: 10px 30px; font-size: 15px; line-height: 22px; background: #FFE3D4; border: solid 1px #FF7348; }
div.info { margin: 20px; padding: 10px 30px; font-size: 15px; line-height: 22px; background: #D4FFD4; border: solid 1px #00B900; }
#header * { margin: 0; padding: 0; }
#header { <?php echo($standard)?> height: 50px; margin: 0; padding: 0; margin-left: 5em; margin-right:5em; margin-top:1em; border-left:#ccc 3px double; border-right:#ccc 3px double; border-top:#ccc 3px double; }
#header > h1 > a { color: #FFFFFF; font-weight: normal; display: block; text-decoration: none; text-align: center; float: left; width: 200px; height: 50px; font-size: 40px; padding-left: 2px; }
#header > h1 > a:hover { color: #C3FFC3; }
#header > ul { display: block; height: 50px; float: right; list-style-type: none; padding-right: 10px; }
#header > ul > li { display: block; float: left; border-left: solid 1px #C3FFC3; height: 50px; }
#header > ul > li > a { display: block; color: #FFFFFF; padding: 13px 15px 7px; text-decoration: none; font-size: 20px; background-color: #5ABF5B; height: 30px; }
#header > ul > li > a:hover { <?php echo($hilight); ?> }
#search { <?php echo($pale)?> border-bottom:1px solid #5ABF5B; margin: 0; padding: 2px; margin-left: 5em; margin-right:5em; border-left:#ccc 3px double; border-right:#ccc 3px double; border-top:#ccc 3px double;}
#content {font-family:Georgia,Serif; background: white; margin-left: 5em; margin-right:5em; margin-top:0; border-left:#ccc 3px double; border-right:#ccc 3px double; border-bottom:#ccc 3px double;}
.gutter {padding: 0.6em; padding-top: 0.1em; }
div.alternates {padding: 6px; }
ul.linear { display: block; float: left; list-style-type: none; padding-right: 10px; }
ul.linear > li { display: block; float: left; padding-right:10px; }
#footer { clear:both; margin-left: 5em; margin-right:5em; text-align:center;}
pre { <?php echo($pale); ?> padding: 2px; border: dotted 1px #5ABF5B; }
table.form { <?php echo($pale)?> border: solid 1px #5ABF5B;}
table.form th { <?php echo($standard)?>; text-align:left; padding: 4px; border-top: 1px solid white;}
table.form th.first { <?php echo($standard)?>; text-align:left; padding: 4px; border-top: 0px;}
table.form td { <?php echo($pale)?>; text-align:left; padding: 4px; border: 1px solid #F6FFF6;}
.summary {font-style: italic; }
.box {width: 20em; background: url(/images/box-bl.gif) no-repeat left bottom; }
.box-outer {background: url(/images/box-br.gif) no-repeat right bottom; padding-bottom:5%; }
.box-inner {background: url(/images/box-tl.gif) no-repeat left top;  }
.box h2 {background: url(/images/box-tr.gif) no-repeat right top; padding-top:5%; }
.box h2, .box ul {padding-left: 5%; padding-right:5%; }

    </style>
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
          echo($type);
          echo '" href="';
          echo($uri);
          echo '" />' . "\n";
        }
      }
      ?>
    
    
    
   </head>

  <body>
    <div id="wrap">
    
      <div id="header">
        <h1><a href="/" title="Home">OpenVocab</a></h1>
        <ul id="nav">
        <?php foreach (array_keys($this->map) as $name): ?>
          <li id="n-<?php e($name); ?>"><a href="<?php e($this->url($name)); ?>"><?php e($this->map[$name]); ?></a></li>
        <?php endforeach; ?>
        </ul>
      </div>
      <div id="search">
        <form action="/termdocs" method="get">
          <label for="q">Find vocabulary terms: </label><input type="text" name="q" id="q" size="40" /> <input type="submit" value="search" />
        </form>
      </div>
    
    
      <div id="content">
        <div class="gutter">
          <?php echo $content; ?>
        </div>
      </div>

      <div id="footer">
        <div class="gutter">
          <p>Service provided by <a href="http://iandavis.com/">Ian Davis</a> using the <a href="http://www.talis.com/platform">Talis Platform</a>. All text and data are in the Public Domain.</p>
        </div>
      </div>
    </div>

  </body>
</html>
