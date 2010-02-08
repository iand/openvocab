<!DOCTYPE HTML SYSTEM>
<html>
  <head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8">
    <meta name="description" content="This is the Semantic Library, a rich view of the bibilographic world.">
    <link rel="stylesheet" href="/css/bp/screen.css" type="text/css" media="screen, projection">
    <link rel="stylesheet" href="/css/bp/print.css" type="text/css" media="print">
    <!--[if lt IE 8]><link rel="stylesheet" href="/css/bp/ie.css" type="text/css" media="screen, projection"><![endif]-->

    <link rel="stylesheet" href="/css/screen.css" type="text/css" media="screen">
    <title>Semantic Library</title>
    <?php if(isset($js)) echo $js; ?>
  </head>
  <body>
    <div class="container">    
      <div id="header" class="span-24 last">
        <h1 id="logo"><a href="/"><img src="/images/logo.png" alt="Semantic Library" width="295" height="38"></a></h1>
      </div>
      <hr class="space">

      <div id="content" class="span-23 prepend-1">
        <p>UNDER CONSTRUCTION</p>
        <p>Currently this site only contains a restricted set of information about popular fiction.</p>
        <p>In the meantime, some sample pages:</p>

        
        <p>People:</p>
        <ul>
          <li><a href="/people/edgar-rice-burroughs.html">Edgar Rice Burroughs</a></li>
          <li><a href="/people/terry-pratchett.html">Terry Pratchett</a></li>
          <li><a href="/people/agatha-christie.html">Agatha Christie</a></li>
          <li><a href="/people/isaac-asimov.html">Isaac Asimov</a></li>
          <li><a href="/people/robert-a-heinlein.html">Robert A. Heinlein</a></li>
          <li><a href="/people/j-k-rowling.html">J. K. Rowling</a></li>
          <li><a href="/people/j-r-r-tolkien.html">J. R. R. Tolkien </a></li>
          <li><a href="/people/mark-twain.html">Mark Twain</a></li>
        </ul>
  
        <p>Series:</p>
        <ul>
          <li><a href="/series/ask-isaac-asimov.html">Ask Isaac Asimov?</a></li>
          <li><a href="/series/student-companions-to-classic-writers.html">Student companions to classic writers</a></li>
          <li><a href="/series/the-oxford-mark-twain.html">The Oxford Mark Twain </a></li>
          <li><a href="/series/mark-twain-and-his-circle-series.html">Mark Twain and his circle series</a></li>
          <li><a href="/series/xanth.html">Xanth</a></li>
          <li><a href="/series/discworld.html">Discworld</a></li>
          <li><a href="/series/dragonriders-of-pern.html">Dragonriders of Pern</a></li>
        </ul>
        
        
        <p>Subjects:</p>
        <ul>
          <li><a href="/subjects/holmes-sherlock-fictitious-character.html">Holmes, Sherlock (Fictitious character)</a></li>
          <li><a href="/subjects/middle-earth-imaginary-place.html">Middle Earth (Imaginary Place)</a></li>
        </ul>

        <p>Others:</p>
        <ul>
          <li>A work: <a href="/works/313.html">http://semanticlibrary.org/works/313.html</a></li>
          <li>An edition/item: <a href="/items/5059.html">http://semanticlibrary.org/items/5059.html</a></li>
          <li>An item with holdings: <a href="/items/18051.html">http://semanticlibrary.org/items/18051.html</a></li>
          <li>A collection (from silkworm): <a href="/collections/4047.html">http://semanticlibrary.org/collections/4047.html</a></li>
          <li>A search: <a href="/search.html?q=asimov">http://semanticlibrary.org/search.html?q=asimov</a></li>
          <li>Another search: <a href="/search.html?q=bilbo+baggins">http://semanticlibrary.org/search.html?q=bilbo+baggins</a></li>
          <li>A publisher (sort of, maybe it's an imprint): <a href="/publishers/004.html">http://semanticlibrary.org/publishers/004.html</a></li>
        </ul>


        <hr class="space">
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

