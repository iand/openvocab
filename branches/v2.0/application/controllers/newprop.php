<?php
require_once MORIARTY_DIR . 'datatable.class.php';

class Newprop extends Controller {
  var $_max_relations = 3;


  function __construct() {
    parent::Controller();
    $this->output->enable_profiler(TRUE);
    $this->load->helper('url');
    $this->load->library('session');
  }

  function index() {
    if (!$this->session->userdata('logged_in')) {
      redirect('login');
      exit;
    }

    $this->load->library('jquery');
    $this->jquery->setExternalJquery('/js/jquery-1.3.2.min.js');
    $this->jquery->addExternalScript('/js/pretty.js');
    $this->jquery->setJqDocumentReady(true);


    $data = array();




    $this->load->helper(array('form', 'url'));

    $this->load->library('form_validation');
    $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
    $this->form_validation->set_rules('slug', 'URI', 'trim|required|callback_slug_check|min_length[5]|max_length[20]|xss_clean');
    $this->form_validation->set_rules('label_en', 'Label', 'trim|required|xss_clean');
    $this->form_validation->set_rules('comment_en', 'Comment', 'trim|required|xss_clean');
    $this->form_validation->set_rules('description_en');
    $this->form_validation->set_rules('functional');
    $this->form_validation->set_rules('inversefunctional');
    $this->form_validation->set_rules('symmetric');
    $this->form_validation->set_rules('transitive');

    $data['max_relations'] = $this->_max_relations;
    $data['domain_count'] = $this->read_multivalue_field('domain', 'Domain');
    $data['range_count'] = $this->read_multivalue_field('range', 'Range');
    $data['subprop_count'] = $this->read_multivalue_field('subprop', 'Sub-property of');
    $data['inverse_count'] = $this->read_multivalue_field('inverse', 'Inverse of');
    $data['equivalent_count'] = $this->read_multivalue_field('equivalent', 'Equivalent to');


    if ($this->form_validation->run() == FALSE) {


      $dt = new DataTable(config_item('store_uri'));
      $dt->map(RDFS_LABEL, 'label');
      $dt->map(RDFS_ISDEFINEDBY, 'isdefinedby');
      $dt->map(RDF_TYPE, 'type');

      $dt->select('label');
      $dt->where_uri('isdefinedby', config_item('resource_base') . '/' . config_item('term_path'));
      $dt->where_uri('type', OWL_CLASS);
      $classes = $dt->get()->result();

      $dt = new DataTable(config_item('store_uri'));
      $dt->map(RDFS_LABEL, 'label');
      $dt->map(RDFS_COMMENT, 'comment');
      $dt->map(RDFS_ISDEFINEDBY, 'isdefinedby');
      $dt->map(RDF_TYPE, 'type');

      $dt->select('label');
      $dt->where_uri('isdefinedby', config_item('resource_base') . '/' . config_item('term_path'));
      $dt->where_uri('type', RDF_PROPERTY);
      $properties = $dt->get()->result();

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
        }

      var properties = [';

      foreach ($properties as $result) {
        $data['js'] .= '{ label:"' . htmlspecialchars($result->label) . '", uri:"' . htmlspecialchars($result->_uri). '" },';
      }

      $data['js'] .= '
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
        ];


        var classes = [';

        foreach ($classes as $result) {
          $data['js'] .= '{ label:"' . htmlspecialchars($result->label) . '", uri:"' . htmlspecialchars($result->_uri). '" },';
        }
        $data['js'] .= '

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
        ];


        var autocomplete_config = {
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
        };




        ';


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
        for ($i = 0; $i <= $data['domain_count']; $i++) {
          $js_ready .= '         $("#domain_' . $i . '").autocomplete(classes, autocomplete_config);' . "\n";
        }
        for ($i = 0; $i <= $data['range_count']; $i++) {
          $js_ready .= '         $("#range_' . $i . '").autocomplete(classes, autocomplete_config);' . "\n";
        }
        for ($i = 0; $i <= $data['subprop_count']; $i++) {
          $js_ready .= '         $("#subprop_' . $i . '").autocomplete(classes, autocomplete_config);' . "\n";
        }
        for ($i = 0; $i <= $data['inverse_count']; $i++) {
          $js_ready .= '         $("#inverse_' . $i . '").autocomplete(classes, autocomplete_config);' . "\n";
        }
        for ($i = 0; $i <= $data['inverse_count']; $i++) {
          $js_ready .= '         $("#inverse_' . $i . '").autocomplete(classes, autocomplete_config);' . "\n";
        }

      $this->jquery->addExternalScript('/js/jquery.autocomplete.js');
      $this->jquery->addJqueryScript($js_ready);
      $data['js'] .= $this->jquery->processJquery();

      $this->load->view('form.editproperty.php', $data);
    }
    else {
      $this->load->model('Term', 'term');
      $this->load->model('TermDescription', 'termdescription');

      $this->term->set_uri(config_item('vocab_uri') . $this->input->post('slug'));
      $this->term->is_defined_by = config_item('resource_base') . '/' . config_item('term_path');
      $doc_path=  '/' . config_item('term_document_path') . config_item('term_delimiter') . $this->input->post('slug');
      $this->term->userdocs = config_item('resource_base') . $doc_path;
      $this->term->status = "unstable";


      $this->term->label = $this->input->post('label_en');
      $this->term->plural = $this->input->post('plural_en');
      $this->term->comment = $this->input->post('comment_en');
      $this->term->is_property = TRUE;
      $this->term->is_class = FALSE;
      if ( $this->input->post('functional') ) {
        $this->term->is_functional = TRUE;
      }
      if ( $this->input->post('inversefunctional') ) {
        $this->term->is_inversefunctional = TRUE;
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


      $response = $this->term->insert_data();

      redirect( $doc_path, 'location', 302);
    }

  }

  // Validation of slug fields
  function slug_check($data) {
    if ( ! preg_match('/^[a-z][a-zA-Z0-9-]+$/', $data) ) {
      $this->form_validation->set_message('slug_check', "The name of the property must be start with a lowercase letter and contain only letters, numbers and/or hyphens");
      return FALSE;
    }
    return TRUE;
  }

  // Validation of URI fields
  function uri_check($url) {
    $ret = TRUE;

    $url = substr($url,-1) == "/" ? substr($url,0,-1) : $url;
    if ( !$url || $url=="" ) return TRUE;
    if ( !( $parts = @parse_url( $url ) ) ) $ret = FALSE;
    else {
        if ( !isset($parts['scheme']) ) $ret = FALSE;
        else if ( !isset($parts['host']) ) $ret = FALSE;
        else if ( $parts['scheme'] != "http"
          && $parts['scheme'] != "https"
          && $parts['scheme'] != "info"
          && $parts['scheme'] != "tag"
          && strtolower($parts['scheme']) != "urn"
          ) $ret = FALSE;
        else if ( !eregi( "^[0-9a-z]([-.]?[0-9a-z])*.[a-z]{2,4}$", $parts['host'], $regs ) ) $ret = FALSE;
        else if (isset($parts['user']) && !eregi( "^([0-9a-z-]|[_])*$", $parts['user'], $regs ) ) $ret = FALSE;
        else if (isset($parts['pass']) && !eregi( "^([0-9a-z-]|[_])*$", $parts['pass'], $regs ) ) $ret = FALSE;
        else if (isset($parts['path']) && !eregi( "^[0-9a-z/_.@~-]*$", $parts['path'], $regs ) ) $ret = FALSE;
        else if (isset($parts['query']) && !eregi( "^[0-9a-z?&=#,]*$", $parts['query'], $regs ) ) $ret = FALSE;
    }

    if ($ret) {
      return TRUE;
    }
    else {
      $this->form_validation->set_message('uri_check', "This is not recognised as a valid URI.");
      return FALSE;
    }
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
    $this->model->$model_field = $values;
  }


}
?>
