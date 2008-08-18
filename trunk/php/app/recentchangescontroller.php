<?php

require_once MORIARTY_DIR . 'store.class.php';
class RecentChangesController extends k_Controller
{
  
  function GET() {
    $vars = array();

    $cs_query =  "prefix cs: <http://purl.org/vocab/changeset/schema#>
prefix rdfs: <http://www.w3.org/2000/01/rdf-schema#>
select ?cs ?soc ?creator ?date ?reason ?title
where {
  ?cs cs:subjectOfChange ?soc ;
      cs:creatorName ?creator ;
      cs:createdDate ?date ;
      cs:changeReason ?reason .
  ?soc rdfs:label ?title .
}
order by desc(?date)
limit 30";

    $store = new Store(STORE_URI);
    $sparql = $store->get_sparql_service();
    $vars['history'] = $sparql->select_to_array($cs_query);

    return $this->render("templates/recentchanges.tpl.php", $vars);
  }
}
?>
