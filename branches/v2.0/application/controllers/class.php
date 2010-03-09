<?php
class ClassController extends Controller {
  var $_max_relations = 3;


  function __construct() {
    parent::Controller();
//    $this->output->enable_profiler(TRUE);
    $this->load->helper('url');
    $this->load->library('session');
  }


  function fill_term() {
    if ($this->input->post('uri')) {
      $this->term->set_uri($this->input->post('uri'));
    }
    else if ($this->input->post('slug') && preg_match('~[a-z][a-zA-z0-9-]~', $this->input->post('slug') )) {
      $this->term->set_uri(config_item('resource_base') . '/' . config_item('term_path') . config_item('term_delimiter') . $this->input->post('slug'));
    }

    $this->term->is_defined_by = config_item('resource_base') . '/' . config_item('term_path');
    $doc_path=  '/' . config_item('term_document_path') . config_item('term_delimiter') . $this->input->post('slug');
    $this->term->userdocs = config_item('resource_base') . $doc_path;
    $this->term->status = "unstable";


    $this->term->label = trim($this->input->post('label_en'));
    $this->term->plural = trim($this->input->post('plural_en'));
    $this->term->comment = trim($this->input->post('comment_en'));
    $this->term->is_property = TRUE;
    $this->term->is_class = FALSE;
    if ( $this->input->post('functional') ) {
      $this->term->is_functional = TRUE;
    }
    if ( $this->input->post('inversefunctional') ) {
      $this->term->is_inverse_functional = TRUE;
    }
    if ( $this->input->post('symmetric') ) {
      $this->term->is_symmetrical = TRUE;
    }
    if ( $this->input->post('transitive') ) {
      $this->term->is_transitive = TRUE;
    }

//      $this->model->description = $this->input->post('description_en');
    $this->set_model_multivalue_field('domain', 'domains');
    $this->set_model_multivalue_field('range', 'ranges');
    $this->set_model_multivalue_field('inverse', 'inverses');
    $this->set_model_multivalue_field('equivalent', 'equivalentproperties');
    $this->set_model_multivalue_field('subprop', 'superproperties');

  }

  function fill_change() {
    $this->change->reason = trim($this->input->post('reason'));
  }

  function add() {
    $this->view = 'add';
    $this->show_form();
  }
  function edit() {
    $this->view = 'edit';
    $this->show_form();
  }

  function show_form() {
    if (!$this->session->userdata('logged_in')) {
      redirect('login');
      exit;
    }

    $this->load->helper(array('form', 'url', 'local_link', 'utility'));
    $this->load->model('Term', 'term');
    $this->load->model('TermChange', 'change');

    $data = array();

    if ($this->input->post('action')) {
      // Form has been submitted

      $this->fill_term();
      $this->fill_change();
      $validation_errors = $this->term->validate() + $this->change->validate();

      $slug = $this->input->post('slug');
      if ($slug) {
        if (! preg_match('~[a-z][a-zA-z0-9-]~', $slug) ) {
          $validation_errors[] = array('field' => 'slug', 'message' => 'Last segment of URI must be mixed case, must start with a lowercase letter, contain only letters, numbers and hyphen.');
        }
        else if($this->term->exists_in_store()) {
          $validation_errors[] = array('field' => 'slug', 'message' => 'The URI you specified is already in use.');
        }
      }


      if (count($validation_errors) == 0) {
        $this->load->model('Term', 'original');
        $this->original->set_uri($this->term->get_uri());
        $this->original->load_from_network(FALSE); // ignore cache

        $response = $this->term->save_to_network($this->original);
        if ($response->is_success()) {
          $this->term->load_from_network(FALSE); // repopulate cache
          redirect( local_link($this->term->get_uri()), 'location', 303);
          exit;
        }
        else {
          $data['error'] = 'Your changes could not be saved. The response from the Talis Platform store was: ' . $response->body . ' (code ' . $response->status_code . ')';
        }
      }
      else {
        $data['error'] = 'The following errors were encountered:<ul>';
        foreach ($validation_errors as $error_info) {
          $data['error'] .= '<li>' . $error_info['message'] . '</li>';
        }
        $data['error'] .= '</ul>';
      }

    }
    else {
      // Form has not been submitted
      $uri = $this->input->get('uri');
      if ($uri) {
        $this->term->set_uri($uri);
        $this->term->load_from_network(FALSE); // ignore cache

        if (!$this->term->has_data() ) {
          $data = array();
          $data['error'] = "No description of " . $uri . " could be found.";
          $this->load->view('form.invalidterm.php', $data);
          return;
        }

        if (!$this->term->is_property() ) {
          $data = array();
          $data['error'] = $uri . " is not a property.";
          $this->load->view('form.invalidterm.php', $data);
          return;
        }
      }
    }

    $this->load->library('jquery');

    $this->jquery->setExternalJquery('/js/jquery-1.3.2.min.js');
    $this->jquery->addExternalScript('/js/pretty.js');
    $this->jquery->setJqDocumentReady(true);


    $data['js'] = '<script type="text/javascript">' . "\n";
    $data['js'] .= '
      function create_textbox(event) {
        last_textbox = $(this).prevAll("input.text:first");
        last_id = last_textbox.attr("id");
        suffix = last_id.substr($(this).attr("id").indexOf("_")+1);
        prefix = last_id.substr(0,$(this).attr("id").indexOf("_"));
        new_id = prefix + "_" + ++suffix;

        if (suffix >= ' . $this->_max_relations . ' - 1) {
          $(this).hide();
        }

        new_textbox = last_textbox.clone();
        new_textbox.attr("id", new_id);
        new_textbox.attr("name", new_id);
        new_textbox.attr("value", "");

        $(this).before("<br />").before(new_textbox);
        if (prefix == "domain" || prefix == "range") {
          $("#" + new_id).autocomplete(classes, autocomplete_config);
        }
        else {
          $("#" + new_id).autocomplete(properties, autocomplete_config);
        }
        // Stop the link click from doing its normal thing
        return false;
      }'. "\n";


    $data['js'] .= $this->get_classes_js_array();
    $data['js'] .= $this->get_properties_js_array();
    $data['js'] .= $this->get_autocomplete_js();

    $data['js'] .= "\n" . '</script>'. "\n";


    $js_ready = '
       $(".more").click(create_textbox);

        $("#label_en").keyup(function() {
          $(".example .label").html( $(this).val() );
        });

       $("#plural_en").keyup(function() {
          $(".example .plural").html( $(this).val() );
        });

      ' . "\n";
      for ($i = 0; $i <= count($this->term->domains); $i++) {
        $js_ready .= '         $("#domain_' . $i . '").autocomplete(classes, autocomplete_config);' . "\n";
      }
      for ($i = 0; $i <= count($this->term->ranges); $i++) {
        $js_ready .= '         $("#range_' . $i . '").autocomplete(classes, autocomplete_config);' . "\n";
      }
      for ($i = 0; $i <= count($this->term->superproperties); $i++) {
        $js_ready .= '         $("#subprop_' . $i . '").autocomplete(properties, autocomplete_config);' . "\n";
      }
      for ($i = 0; $i <= count($this->term->inverses); $i++) {
        $js_ready .= '         $("#inverse_' . $i . '").autocomplete(properties, autocomplete_config);' . "\n";
      }
      for ($i = 0; $i <= count($this->term->equivalentproperties); $i++) {
        $js_ready .= '         $("#equivalent_' . $i . '").autocomplete(properties, autocomplete_config);' . "\n";
      }

    $this->jquery->addExternalScript('/js/jquery.autocomplete.js');
    $this->jquery->addJqueryScript($js_ready);
    $data['js'] .= $this->jquery->processJquery();
    $data['term'] = $this->term;
    $data['max_relations'] = $this->_max_relations;
    if ($this->view == 'edit') {
      $data['form_action'] = '/forms/editprop';
      $data['page_title'] = 'Edit Property';
      $data['show_slug'] = FALSE;
      $data['show_reason'] = TRUE;
      $data['uri'] = $this->term->get_uri();
      $data['cancel_link'] = local_link($data['uri']);
      $data['reason'] = $this->input->post('reason');
    }
    else {
      $data['form_action'] = '/forms/newprop';
      $data['page_title'] = 'New Property';
      $data['show_slug'] = TRUE;
      $data['show_reason'] = FALSE;
      $data['cancel_link'] = '/' . config_item('term_document_path');
      $data['slug'] = $this->input->post('slug');
    }
    $this->load->view('form.editproperty.php', $data);

  }



  function read_multivalue_field($name, $label) {

    $max = 0;
    for ($i = 0; $i < $this->_max_relations; $i++) {
      if ($this->input->post($name . '_' . $i)) $max = $i + 1;
    }

    for ($i = 0; $i < $max; $i++) {
      $this->form_validation->set_rules($name . '_' . $i, $label, 'trim|callback_uri_check|xss_clean');
    }

    return $max;
  }

  function set_model_multivalue_field($name, $model_field) {
    $values = array();
    for ($i = 0; $i < $this->_max_relations; $i++) {
      if ($this->input->post($name . '_' . $i)) {
        $values[] = $this->input->post($name . '_' . $i);
      }
    }
    $this->term->$model_field = $values;
  }

  function get_classes_js_array() {
    $dt = new DataTable(config_item('store_uri'));
    $dt->map(RDFS_LABEL, 'label');
    $dt->map(RDFS_ISDEFINEDBY, 'isdefinedby');
    $dt->map(RDF_TYPE, 'type');

    $dt->select('label');
    $dt->where_uri('isdefinedby', config_item('resource_base') . '/' . config_item('term_path'));
    $dt->where_uri('type', OWL_CLASS);
    $classes = $dt->get()->result();

    $js= 'var classes = [';

    foreach ($classes as $result) {
      $js .= '{ label:"' . htmlspecialchars($result->label) . '", uri:"' . htmlspecialchars($result->_uri). '" },';
    }
    $js .= '

        { label: "foaf:Agent", uri: "http://xmlns.com/foaf/0.1/Agent" },
        { label: "foaf:Document", uri: "http://xmlns.com/foaf/0.1/Document" },
        { label: "foaf:Person", uri: "http://xmlns.com/foaf/0.1/Person" },
        { label: "foaf:Image", uri: "http://xmlns.com/foaf/0.1/Image" },
        { label: "foaf:OnlineAccount", uri: "http://xmlns.com/foaf/0.1/OnlineAccount" },
        { label: "foaf:Organization", uri: "http://xmlns.com/foaf/0.1/Organization" },
        { label: "foaf:Project", uri: "http://xmlns.com/foaf/0.1/Project" },
        { label: "foaf:Group", uri: "http://xmlns.com/foaf/0.1/Group" },
        { label: "rdfs:Literal", uri: "http://www.w3.org/2000/01/rdf-schema#Literal" },
        { label: "geo:SpatialThing", uri: "http://www.w3.org/2003/01/geo/wgs84_pos#SpatialThing" },
        { label: "sioc:Community", uri: "http://rdfs.org/sioc/ns#Community" },
        { label: "sioc:Forum", uri: "http://rdfs.org/sioc/ns#Forum" },
        { label: "sioc:Item", uri: "http://rdfs.org/sioc/ns#Item" },
        { label: "sioc:Post", uri: "http://rdfs.org/sioc/ns#Post" },
        { label: "sioc:Site", uri: "http://rdfs.org/sioc/ns#Site" },
        { label: "sioc:User", uri: "http://rdfs.org/sioc/ns#User" },
        { label: "skos:Concept", uri: "http://www.w3.org/2008/05/skos#Concept" },
        { label: "bio:Event", uri: "http://purl.org/vocab/bio/0.1/Event" },
        { label: "doap:Project", uri: "http://usefulinc.com/ns/doap#Project" },
      ];' . "\n";

      return $js;
    }

    function get_properties_js_array() {
      $dt = new DataTable(config_item('store_uri'));
      $dt->map(RDFS_LABEL, 'label');
      $dt->map(RDFS_COMMENT, 'comment');
      $dt->map(RDFS_ISDEFINEDBY, 'isdefinedby');
      $dt->map(RDF_TYPE, 'type');

      $dt->select('label');
      $dt->where_uri('isdefinedby', config_item('resource_base') . '/' . config_item('term_path'));
      $dt->where_uri('type', RDF_PROPERTY);
      $properties = $dt->get()->result();

      $js= 'var properties = [';

      foreach ($properties as $result) {
        $js .= '{ label:"' . htmlspecialchars($result->label) . '", uri:"' . htmlspecialchars($result->_uri). '" },';
      }

      $js .= '
        { label: "foaf:knows", uri: "http://xmlns.com/foaf/0.1/knows" },

        { label: "rdfs:label", uri: "http://www.w3.org/2000/01/rdf-schema#label" },
        { label: "rdfs:comment", uri: "http://www.w3.org/2000/01/rdf-schema#comment" },
        { label: "rdfs:isDefinedBy", uri: "http://www.w3.org/2000/01/rdf-schema#isDefinedBy" },
        { label: "rdfs:seeAlso", uri: "http://www.w3.org/2000/01/rdf-schema#seeAlso" },

        { label: "foaf:isPrimaryTopicOf", uri: "http://xmlns.com/foaf/0.1/isPrimaryTopicOf" },
        { label: "foaf:nick", uri: "http://xmlns.com/foaf/0.1/nick" },
        { label: "foaf:name", uri: "http://xmlns.com/foaf/0.1/name" },
        { label: "foaf:primaryTopic", uri: "http://xmlns.com/foaf/0.1/primaryTopic" },
        { label: "foaf:topic", uri: "http://xmlns.com/foaf/0.1/topic" },
        { label: "foaf:page", uri: "http://xmlns.com/foaf/0.1/page" },
        { label: "foaf:img", uri: "http://xmlns.com/foaf/0.1/img" },
        { label: "foaf:depiction", uri: "http://xmlns.com/foaf/0.1/depiction" },
        { label: "foaf:depicts", uri: "http://xmlns.com/foaf/0.1/depicts" },
        { label: "foaf:homepage", uri: "http://xmlns.com/foaf/0.1/homepage" },
        { label: "foaf:weblog", uri: "http://xmlns.com/foaf/0.1/weblog" },
        { label: "foaf:surname", uri: "http://xmlns.com/foaf/0.1/surname" },
        { label: "foaf:givenname", uri: "http://xmlns.com/foaf/0.1/givenname" },
        { label: "foaf:interest", uri: "http://xmlns.com/foaf/0.1/interest" },
        { label: "foaf:made", uri: "http://xmlns.com/foaf/0.1/made" },
        { label: "foaf:maker", uri: "http://xmlns.com/foaf/0.1/maker" },
        { label: "foaf:based_near", uri: "http://xmlns.com/foaf/0.1/based_near" },
        { label: "foaf:member", uri: "http://xmlns.com/foaf/0.1/member" },

        { label: "dc:title", uri: "http://purl.org/dc/elements/1.1/title" },
        { label: "dc:description", uri: "http://purl.org/dc/elements/1.1/description" },
        { label: "dc:creator", uri: "http://purl.org/dc/elements/1.1/creator" },
        { label: "dc:date", uri: "http://purl.org/dc/elements/1.1/date" },
        { label: "dc:rights", uri: "http://purl.org/dc/elements/1.1/rights" },
        { label: "dc:subject", uri: "http://purl.org/dc/elements/1.1/subject" },
        { label: "skos:subject", uri: "http://www.w3.org/2004/02/skos/core#subject" },
        { label: "skos:isSubjectOf", uri: "http://www.w3.org/2004/02/skos/core#isSubjectOf" }
        ];' . "\n";
      return $js;
    }

    function get_autocomplete_js() {

    $js= 'var autocomplete_config = {
          minChars: 1,
          width: 400,
          matchContains: true,
          autoFill: false,
          formatItem: function(row, i, max) {
            return row.label + " (" + row.uri + ")";
          },
          formatMatch: function(row, i, max) {
            return row.label + " " + row.uri;
          },
          formatResult: function(row) {
            return row.uri;
          }
        };' .  "\n";

    return $js;
  }

}
?>
