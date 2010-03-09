<?php
require_once MORIARTY_DIR . 'moriarty.inc.php';
require_once MORIARTY_DIR . 'credentials.class.php';
require_once MORIARTY_DIR . 'httprequestfactory.class.php';
require_once MORIARTY_DIR . 'simplegraph.class.php';
require_once MORIARTY_DIR . 'store.class.php';


class RDFModel extends Model {
  var $graph;
  protected $_fields = array();
  protected $_fdefs = array();
  protected $_resources = array();
  protected $_has_data = FALSE;
  var $_uri;

  function set_uri($uri) {
    $this->_uri = $uri;
  }

  function get_uri() {
    return $this->_uri;
  }

  /**
   * var_type is one of 'b' for boolean, 'f' for single-valued field, 'a' for array
   */
  function add_field($short_name, $property_uri, $resource, $var_type, $value_type, $value_uri) {
    if ($var_type == 'f') $var_type = 'scalar';
    else if ($var_type == 'b') $var_type = 'boolean';
    else if ($var_type == 'a') $var_type = 'array';

    $handler = $value_type . '_' . $var_type;


    $this->_fields[$short_name] = array('property_uri' => $property_uri, 'resource' => $resource, 'var_type' => $var_type, 'value_type' => $value_type, 'value_uri' => $value_uri);
    $this->_fdefs[] = array('short_name' => $short_name, 'property_uri' => $property_uri, 'handler' => $handler);

  }

  /**
   * Define a field on the object that maps to a property. The order in which
   * fields are defined is preserved so properties can refer to earlier
   * property values if need be.
   **/
  function define_field($short_name, $property_uri, $handler = 'literal_scalar') {
    $this->_fdefs[] = array('short_name' => $short_name, 'property_uri' => $property_uri, 'handler' => $handler);
    $method = 'init_' . $handler;
    if (method_exists($this, $method)) {
      $this->$method($short_name, $property_uri);
    }
    else {
      $this->init_literal_scalar($short_name, $property_uri);
    }
  }

  /**
   * Read field values from graph.
   **/
  function read_fields() {
    foreach ($this->_fdefs as $field_info) {
      $method = 'read_' . $field_info['handler'];
      if (method_exists($this, $method)) {
        $this->$method($field_info['short_name'], $field_info['property_uri']);
      }
    }
  }

  /**
   * Write field values to graph.
   **/
  function write_fields() {
    $this->graph = new SimpleGraph();
    $this->graph->set_namespace_mapping(config_item('vocab_prefix'), config_item('vocab_uri'));
    $this->graph->update_prefix_mappings();
    $this->collect_triples();
    foreach ($this->_fdefs as $field_info) {
      $read_method = 'write_' . $field_info['handler'];
      if (method_exists($this, $read_method)) {
        $this->$read_method($field_info['short_name'], $field_info['property_uri']);
      }
    }
  }

  /**
   * Add any ad-hoc triples to the graph in addition to those held in fields. Designed to be overriden.
   **/
  function collect_triples() {
    // NOOP
  }

  /**
   * Read a literal value from the graph into a scalar field.
   **/
  function read_literal_scalar($short_name, $property_uri) {
    $this->$short_name = $this->graph->get_first_literal($this->_uri, $property_uri, NULL);
    if ($this->$short_name !== NULL ) $this->_has_data = TRUE;
  }

  /**
   * Write a literal value held in a scalar field to the graph, replacing any existing values.
   **/
  function write_literal_scalar($short_name, $property_uri) {
    $this->graph->remove_property_values($this->_uri, $property_uri);
    $this->graph->add_literal_triple($this->_uri, $property_uri, $this->$short_name);
  }

  /**
   * Initialise a literal scalar field.
   **/
  function init_literal_scalar($short_name, $property_uri) {
    $this->$short_name = '';
  }


  /**
   * Read a resource value from the graph into a scalar field.
   **/
  function read_resource_scalar($short_name, $property_uri) {
    $this->$short_name = $this->graph->get_first_resource($this->_uri, $property_uri, NULL);
    if ($this->$short_name !== NULL ) $this->_has_data = TRUE;
  }

  /**
   * Write a resource value held in a scalar field to the graph, replacing any existing values.
   **/
  function write_resource_scalar($short_name, $property_uri) {
    $this->graph->remove_property_values($this->_uri, $property_uri);
    $this->graph->add_resource_triple($this->_uri, $property_uri, $this->$short_name);
  }


  /**
   * Initialise a resource scalar field.
   **/
  function init_resource_scalar($short_name, $property_uri) {
    $this->$short_name = '';
  }

  /**
   * Read literal values from the graph into an array field.
   **/
  function read_literal_array($short_name, $property_uri) {
    $this->$short_name = $this->graph->get_literal_triple_values($this->_uri, $property_uri);
    if (count($this->$short_name) > 0) $this->_has_data = TRUE;
  }

  /**
   * Write literal values held in an array field to the graph, replacing any existing values.
   **/
  function write_literal_array($short_name, $property_uri) {
    $this->graph->remove_property_values($this->_uri, $property_uri);
    foreach ($this->$short_name as $value) {
      $this->graph->add_literal_triple($this->_uri, $property_uri, $value);
    }
  }

  /**
   * Initialise a literal array field.
   **/
  function init_literal_array($short_name, $property_uri) {
    $this->$short_name = array();
  }


  /**
   * Read resource values from the graph into an array field.
   **/
  function read_resource_array($short_name, $property_uri) {
    $this->$short_name = $this->graph->get_resource_triple_values($this->_uri, $property_uri);
    if (count($this->$short_name) > 0) $this->_has_data = TRUE;
  }

  /**
   * Write resource values held in an array field to the graph, replacing any existing values.
   **/
  function write_resource_array($short_name, $property_uri) {
    $this->graph->remove_property_values($this->_uri, $property_uri);
    foreach ($this->$short_name as $value) {
      $this->graph->add_resource_triple($this->_uri, $property_uri, $value);
    }
  }

  /**
   * Initialise a resource array field.
   **/
  function init_resource_array($short_name, $property_uri) {
    $this->$short_name = array();
  }



  /**
   * Read a resource value from the graph into a scalar field, inverting the property relationship.
   **/
  function read_inverse_scalar($short_name, $property_uri) {
    $subjects = $this->graph->get_subjects_where_resource($property_uri, $this->_uri);
    if (count($subjects) > 0) {
      $this->$short_name = $subjects[0];
      if ($this->$short_name !== NULL ) $this->_has_data = TRUE;
    }
  }

  /**
   * Write a resource value held in a scalar field to the graph, inverting the property relationship and replacing any existing values.
   **/
  function write_inverse_scalar($short_name, $property_uri) {
    $this->graph->remove_property_values($this->_uri, $property_uri);
    $this->graph->add_resource_triple($this->$short_name, $property_uri, $this->_uri);
  }


  /**
   * Initialise an inverse scalar field.
   **/
  function init_inverse_scalar($short_name, $property_uri) {
    $this->$short_name = '';
  }



  function get_non_caching_readable_store() {
    $request_factory = new HttpRequestFactory();
    $request_factory->read_from_cache(FALSE);
    $store = new Store(config_item('store_uri'), null, $request_factory);
    return $store;
  }

  function get_readable_store() {
    $store = new Store(config_item('store_uri'), null);
    return $store;
  }

  function get_writeable_store() {
    $request_factory = new HttpRequestFactory();
    $request_factory->read_from_cache(FALSE);
    $store = new Store(config_item('store_uri'), new Credentials(config_item('store_user'), config_item('store_pwd')), $request_factory);
    return $store;
  }

  function populate_graph($default_store) {
    $response= $default_store->describe($this->_uri, 'cbd', 'json');
    if ($response->is_success()) {
      $this->graph->add_json($response->body);
    }
  }

  function load_from_network($read_from_cache = TRUE) {
    $this->_has_data = FALSE;

    $this->graph = new SimpleGraph();
    $this->graph->set_namespace_mapping(config_item('vocab_prefix'), config_item('vocab_uri'));
    $this->graph->update_prefix_mappings();

    if ($read_from_cache) {
      $store = $this->get_readable_store();
    }
    else {
      $store = $this->get_non_caching_readable_store();
    }
    $this->populate_graph($store);

    $this->read_fields();
  }


  function save_to_network($previous = NULL) {
    if ($previous !== NULL && $previous->_has_data) {
      $cs = $this->get_diff($previous);
      $store = new Store(config_item('store_uri'), new Credentials(config_item('store_user'), config_item('store_pwd')));
      $mb = $store->get_metabox();
      $changesets = $cs->get_subjects_of_type(CS_CHANGESET);
      if (count($changesets) > 0 &&
        ( count($cs->get_resource_triple_values($changesets[0], CS_REMOVAL)) > 0 || count($cs->get_resource_triple_values($changesets[0], CS_ADDITION)) > 0 )

        ) {
        //printf("<p><strong>Posting a changeset</strong></p><pre>%s</pre>", htmlspecialchars($cs->to_turtle()));
        return $mb->apply_changeset_rdfxml( $cs->to_rdfxml() );
      }
    }
    else {
      $this->write_fields();

      $store = $this->get_writeable_store();
      return $store->store_data($this->graph);
    }
  }

  function delete_from_network() {

  }


  function get_diff($previous) {

    $this->write_fields();
    $previous->write_fields();


    $node_index= 0;
    $cs = new SimpleGraph();
    $cs_subj = '_:cs' . $node_index++;
    $cs->add_resource_triple($cs_subj, RDF_TYPE, CS_CHANGESET);
    $cs->add_resource_triple($cs_subj, CS_SUBJECTOFCHANGE, $this->_uri);
    $cs->add_literal_triple($cs_subj, CS_CHANGEREASON, "Update");
    $cs->add_literal_triple($cs_subj, CS_CREATEDDATE, gmdate(DATE_ATOM));
    $cs->add_literal_triple($cs_subj, CS_CREATORNAME, "Moriarty");

    $additions = SimpleGraph::diff($this->graph->get_index(), $previous->graph->get_index());
    $removals = SimpleGraph::diff($previous->graph->get_index(), $this->graph->get_index());

    if (count($removals) > 0 && array_key_exists($this->_uri, $removals)) {
      foreach ($removals[$this->_uri] as $p => $p_list) {
        foreach ($p_list as $p_info) {
          $node = '_:r' . $node_index;
          $cs->add_resource_triple($cs_subj, CS_REMOVAL, $node);
          $cs->add_resource_triple($node, RDF_TYPE, RDF_STATEMENT);
          $cs->add_resource_triple($node, RDF_SUBJECT, $this->_uri);
          $cs->add_resource_triple($node, RDF_PREDICATE, $p);
          if ($p_info['type'] === 'literal')  {
            $dt = array_key_exists('datatype', $p_info) ? $p_info['datatype'] : null;
            $lang = array_key_exists('lang', $p_info) ? $p_info['lang'] : null;
            $cs->add_literal_triple($node, RDF_OBJECT, $p_info['value'], $lang, $dt);
          }
          else {
            $cs->add_resource_triple($node, RDF_OBJECT, $p_info['value']);
          }
          $node_index++;
        }
      }
    }

    if (count($additions) > 0 && array_key_exists($this->_uri, $additions)) {
      foreach ($additions[$this->_uri] as $p => $p_list) {
        foreach ($p_list as $p_info) {
          $node = '_:a' . $node_index;
          $cs->add_resource_triple($cs_subj, CS_ADDITION, $node);
          $cs->add_resource_triple($node, RDF_TYPE, RDF_STATEMENT);
          $cs->add_resource_triple($node, RDF_SUBJECT, $this->_uri);
          $cs->add_resource_triple($node, RDF_PREDICATE, $p);
          if ($p_info['type'] === 'literal')  {
            $dt = array_key_exists('datatype', $p_info) ? $p_info['datatype'] : null;
            $lang = array_key_exists('lang', $p_info) ? $p_info['lang'] : null;
            $cs->add_literal_triple($node, RDF_OBJECT, $p_info['value'], $lang, $dt);
          }
          else {
            $cs->add_resource_triple($node, RDF_OBJECT, $p_info['value']);
          }
          $node_index++;
        }
      }
    }

/*
printf("<h2>This Graph</h2><pre>%s</pre>", htmlspecialchars($this->graph->to_turtle()));
printf("<h2>Previous Graph</h2><pre>%s</pre>", htmlspecialchars($previous->graph->to_turtle()));
printf("<h2>Changsest</h2><pre>%s</pre>", htmlspecialchars($cs->to_turtle()));
*/
    return $cs;

  }




  function exists_in_store() {
    $store = $this->get_non_caching_readable_store();
    $response= $store->describe($this->_uri, 'cbd', 'json');
    if ($response->is_success()) {
      $graph = new SimpleGraph();
      $graph->add_json($response->body);
      return $g->has_triples_about($this->_uri);
    }
    return FALSE;
  }


  function has_data() {
    return $this->_has_data;
  }

  function get_qname() {
    $this->write_fields();
    return $this->graph->uri_to_qname($this->_uri);
  }

  function to_rdfxml() {
    $this->write_fields();
    return $this->graph->to_rdfxml();
  }

  function to_json() {
    $this->write_fields();
    return $this->graph->to_json();
  }

  function to_turtle() {
    $this->write_fields();
    return $this->graph->to_turtle();
  }

  function dump_update_info() {
    echo sprintf('<h2>SPARQL</h2><pre>%s</pre>', htmlspecialchars($dt->get_sparql()));
    echo sprintf('<h2>Results</h2><pre>%s</pre>', htmlspecialchars($dt->get()->to_string()));
    echo sprintf('<h2>Changeset</h2><pre>%s</pre>', htmlspecialchars($dt->get_update_changeset()->to_turtle()));
    echo sprintf('<h2>Insert Graph</h2><pre>%s</pre>', htmlspecialchars($dt->get_insert_graph()->to_turtle()));
  }


}
