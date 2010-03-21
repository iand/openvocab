<?php
require_once 'termcontroller.php';

class PropertyController extends TermController {
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

      $this->fill_term(TRUE);
      $this->fill_change();

//printf("<h2>CHANGE</h2><pre>%s</pre>", htmlspecialchars($this->change->to_turtle()));
//exit;

      $validation_errors = $this->term->validate() + $this->change->validate();

      $slug = $this->input->post('slug');
      if ($slug) {
        if (! preg_match('~^[a-z][a-zA-z0-9-]+$~', $slug) ) {
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
          $this->change->after = $this->term->to_turtle();
          $this->change->before = $this->original->to_turtle();

          $response = $this->change->save_to_network();
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

}


?>
