      <div class="span-12 prepend-top"><h1 id="pagetitle"><a href="/"><?php echo config_item('site_name'); ?></a></h1></div>
      <div class="span-12 large last" style="text-align:right">

        <a href="/<?php echo(htmlspecialchars(config_item('term_document_path'))); ?>">Terms</a> |
        <a href="/create">Create</a> |
        <a href="/changes">Recent Changes</a> |
        <?php if (isset($this->session)) { ?>
        <?php if (!$this->session->userdata('logged_in') ) {?>
          <a href="/login">Login</a> |
        <?php } else { ?>
          <a href="/logout">Logout</a> |
        <?php } ?>
        <?php } ?>
        <a href="/about">About</a>
        <br />
        <?php if (isset($this->session) && $this->session->userdata('logged_in') && $this->session->userdata('username')) { echo '<span class="username quiet">Logged in as ' . $this->session->userdata('username') . '</span>'; } ?>
      </div>
    <hr>
