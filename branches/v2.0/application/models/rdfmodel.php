<?php
require_once MORIARTY_DIR . 'moriarty.inc.php';
require_once MORIARTY_DIR . 'datatable.class.php';
require_once MORIARTY_DIR . 'credentials.class.php';

class RDFModel extends Model {
  var $graph;
  protected $_fields = array();
  protected $_has_data = FALSE;
  var $_uri;


  function set_uri($uri) {
    $this->_uri = $uri;
  }

  function get_uri() {
    return $this->_uri;
  }

  /**
   * type is one of 'b' for boolean, 'l' for literal, 'a' for array
   */
  function add_field($short_name, $property_uri, $type = 'l', $value_type = 'literal', $value = null) {
    if ($type === 'b') {
      $this->$short_name = FALSE;
    }
    else if ($type === 'a') {
      $this->$short_name = array();
    }
    else {
      $this->$short_name = '';
    }
    $this->_fields[$short_name] = array('property_uri' => $property_uri, 'type' => $type, 'value_type' => $value_type, 'value' => $value);
  }

  protected function get_datatable($store_uri, $credentials) {
    $dt = new DataTable($store_uri, $credentials);
    foreach ($this->_fields as $short_name => $field_info) {
      $dt->map($field_info['property_uri'],$short_name);
    }
    return $dt;
  }


  function read_data() {
    $this->_has_data = FALSE;
    $this->graph = new SimpleGraph();
    $this->graph->set_namespace_mapping(config_item('vocab_prefix'), config_item('vocab_uri'));
    $this->graph->update_prefix_mappings();

    $store = new Store(config_item('store_uri'), null);
    $response= $store->describe($this->_uri, 'cbd', 'json');
    if ($response->is_success()) {
      $this->graph->add_json($response->body);

      foreach ($this->_fields as $short_name => $field_info) {
        $vals = $this->graph->get_subject_property_values($this->_uri, $field_info['property_uri']);
        if (count($vals) > 0) {
          $this->_has_data = TRUE;
          if ($field_info['type'] === 'b') {
            if ($this->graph->has_resource_triple($this->_uri, $field_info['property_uri'], $field_info['value'])) {
              $this->$short_name = TRUE;
            }
            else {
              $this->$short_name = FALSE;
            }
          }
          else if ($field_info['type'] === 'a') {
            foreach ($vals as $val_info) {
              array_push($this->$short_name, $val_info['value']);
            }
          }
          else {
            $this->$short_name = $vals[0]['value'];
          }
        }
      }
    }

  }

  function insert_data() {
    $dt = $this->get_datatable(config_item('store_uri'), new Credentials(config_item('store_user'), config_item('store_pwd')) );
    $dt->set('_uri', $this->get_uri());
    foreach ($this->_fields as $short_name => $field_info) {
      if ($this->$short_name) {
        if ($field_info['type'] === 'b') {
          if ($this->$short_name) {
            $dt->set($short_name, $this->_fields[$short_name]['value'], $this->_fields[$short_name]['value_type']);
          }
        }
        else if ($field_info['type'] === 'a') {
          $property_uri = $this->_fields[$short_name]['property_uri'];
          $i = 0;
          foreach ($this->$short_name as $value) {
            if ($value) {
              $dt->map($property_uri, $short_name . '_' . $i);
              $dt->set($short_name . '_' . $i, $value, $this->_fields[$short_name]['value_type']);
              $i++;
            }
          }

  //        $this->$short_name = array();
        }
        else {
          $dt->set($short_name, $this->$short_name, $this->_fields[$short_name]['value_type']);
        }
      }
    }


//    echo sprintf('<h2>SPARQL</h2><pre>%s</pre>', htmlspecialchars($dt->get_sparql()));
//    echo sprintf('<h2>Results</h2><pre>%s</pre>', htmlspecialchars($dt->get()->to_string()));
//    echo sprintf('<h2>Changeset</h2><pre>%s</pre>', htmlspecialchars($dt->get_update_changeset()->to_turtle()));
//    echo sprintf('<h2>Insert Graph</h2><pre>%s</pre>', htmlspecialchars($dt->get_insert_graph()->to_turtle()));
/*
    if ($this->_uri->get_value()) {
*/
      $response = $dt->update();
/*
    }
    else {
      $response = $dt->insert();
    }

*/
    return $response;
  }

  function has_data() {
    return $this->_has_data;
  }

  function get_qname() {
    return $this->graph->uri_to_qname($this->_uri);
  }

  function to_rdfxml() {
    return $this->graph->to_rdfxml();
  }

  function to_json() {
    return $this->graph->to_json();
  }

  function to_turtle() {
    return $this->graph->to_turtle();
  }

}
