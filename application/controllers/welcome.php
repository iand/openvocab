<?php

class Welcome extends Controller {

  function Welcome()
  {
    parent::Controller(); 
  }
  
  function index()
  {
    $data = array();
    $data['content'] = '';

    $data['content'] .= '    <p>Currently this site only contains a restricted set of information about popular fiction.</p>';
    $data['content'] .= '    <p>In the meantime, some sample pages:</p>';

        
    $data['content'] .= '    <p>People:</p>';
    $data['content'] .= '    <ul>';
    $data['content'] .= '      <li><a href="/people/edgar-rice-burroughs.html">Edgar Rice Burroughs</a></li>';
    $data['content'] .= '      <li><a href="/people/terry-pratchett.html">Terry Pratchett</a></li>';
    $data['content'] .= '      <li><a href="/people/agatha-christie.html">Agatha Christie</a></li>';
    $data['content'] .= '      <li><a href="/people/isaac-asimov.html">Isaac Asimov</a></li>';
    $data['content'] .= '      <li><a href="/people/robert-a-heinlein.html">Robert A. Heinlein</a></li>';
    $data['content'] .= '      <li><a href="/people/j-k-rowling.html">J. K. Rowling</a></li>';
    $data['content'] .= '      <li><a href="/people/j-r-r-tolkien.html">J. R. R. Tolkien</a></li>';
    $data['content'] .= '      <li><a href="/people/mark-twain.html">Mark Twain</a></li>';
    $data['content'] .= '    </ul>';
  
    $data['content'] .= '    <p>Series:</p>';
    $data['content'] .= '    <ul>';
    $data['content'] .= '      <li><a href="/series/ask-isaac-asimov.html">Ask Isaac Asimov?</a></li>';
    $data['content'] .= '      <li><a href="/series/student-companions-to-classic-writers.html">Student companions to classic writers</a></li>';
    $data['content'] .= '      <li><a href="/series/the-oxford-mark-twain.html">The Oxford Mark Twain </a></li>';
    $data['content'] .= '      <li><a href="/series/mark-twain-and-his-circle-series.html">Mark Twain and his circle series</a></li>';
    $data['content'] .= '      <li><a href="/series/xanth.html">Xanth</a></li>';
    $data['content'] .= '      <li><a href="/series/discworld.html">Discworld</a></li>';
    $data['content'] .= '      <li><a href="/series/dragonriders-of-pern.html">Dragonriders of Pern</a></li>';
    $data['content'] .= '    </ul>';
        
        
    $data['content'] .= '    <p>Subjects:</p>';
    $data['content'] .= '    <ul>';
    $data['content'] .= '      <li><a href="/subjects/holmes-sherlock-fictitious-character.html">Holmes, Sherlock (Fictitious character)</a></li>';
    $data['content'] .= '      <li><a href="/subjects/middle-earth-imaginary-place.html">Middle Earth (Imaginary Place)</a></li>';
    $data['content'] .= '    </ul>';

    $data['content'] .= '    <p>Others:</p>';
    $data['content'] .= '    <ul>';
    $data['content'] .= '      <li>A work: <a href="/works/313.html">http://semanticlibrary.org/works/313.html</a></li>';
    $data['content'] .= '      <li>An edition/item: <a href="/items/5059.html">http://semanticlibrary.org/items/5059.html</a></li>';
    $data['content'] .= '      <li>An item with holdings: <a href="/items/18051.html">http://semanticlibrary.org/items/18051.html</a></li>';
    $data['content'] .= '      <li>A collection (from silkworm): <a href="/collections/4047.html">http://semanticlibrary.org/collections/4047.html</a></li>';
    $data['content'] .= '      <li>A search: <a href="/search.html?q=asimov">http://semanticlibrary.org/search.html?q=asimov</a></li>';
    $data['content'] .= '      <li>Another search: <a href="/search.html?q=bilbo+baggins">http://semanticlibrary.org/search.html?q=bilbo+baggins</a></li>';
    $data['content'] .= '      <li>A publisher (sort of, maybe it\'s an imprint): <a href="/publishers/004.html">http://semanticlibrary.org/publishers/004.html</a></li>';
    $data['content'] .= '    </ul>';

    $data['description'] = "The Semantic Library provides a rich, structured view of the world's books.";
    $data['links'] = array();
    $data['type'] = 'html';
    $data['title_encoded']  = '        <form action="/search.html" method="get">';
    $data['title_encoded'] .= '          <input class="text" name="q" id="q" value="" type="text" style="width:400px; vertical-align:middle;"> <input name="search" value="Search" type="image" src="/images/srch.jpg" vertical-align:middle;>';
    $data['title_encoded'] .= '        </form>';
    
    
    $this->load->view('resourcedescriptionview', $data);
  }
}
