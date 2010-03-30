<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN"
   "http://www.w3.org/TR/html4/strict.dtd">

<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>About <?= htmlspecialchars(config_item('vocab_name')); ?></title>
    <meta name="description" content="Anyone can participate in creating the <?= htmlspecialchars(config_item('vocab_name')); ?> schema." />

    <!-- Framework CSS -->
    <link rel="stylesheet" href="/css/blueprint/screen.css" type="text/css" media="screen, projection">
    <link rel="stylesheet" href="/css/blueprint/print.css" type="text/css" media="print">
    <!--[if lt IE 8]><link rel="stylesheet" href="/css/blueprint/ie.css" type="text/css" media="screen, projection"><![endif]-->

    <link rel="stylesheet" href="/css/screen.css" type="text/css" media="screen, projection">
    <?php if(isset($js)) echo $js; ?>

<?php
  if (isset($links)) {
    foreach ($links as $link) {
      echo '    <link rel="alternate" type="' . htmlspecialchars($link['type']) . '" href="' . htmlspecialchars($link['href']) . '" title="' . htmlspecialchars($link['title']) . '">' . "\n";
    }
  }
?>
  </head>
  <body>
    <div class="container">
    <?php require_once('header.inc.php'); ?>
    <h2 class="bottom">About</h2>
    <hr>
      <div class="span-24">
        <p>OpenVocab is a project created by <a href="http://iandavis.com/">Ian Davis</a> that enables anyone to participate in the creation of a open and shared RDF vocabulary.
        The project uses wiki principles to allow properties and classes to be created in the vocabulary.</p>

        <p>The code for this project is hosted at <a href="http://code.google.com/p/openvocab/">Google Code</a>. Please submit any feature requests or defect reports using the
        project <a href="http://code.google.com/p/openvocab/issues/list">issue tracker</a>. </p>

        <h3 id="rights">Rights</h3>
        <p>All text and data available via this site are in the Public Domain. </p>

        <h3 id="availability">Availability</h3>
        <p>In short, I will endeavour to keep this site available permanently.</p>

        <p>More formally, the provider of this service is Ian Davis who undertakes to make representations of the resources denoted by URIs defined on this site permanently available. In the event that circumstances prevent Ian Davis from fulfilling these obligations then he will pass ownership of the domain name and any associated content to a suitable party under terms that shall place no additional restrictions on the usage or accessibility of the representations provided.</p>

        <h3 id="privacy">Privacy</h3>
        <p>We make no effort to identify public users of our site. No
        identifying data is disclosed to any third party for any purpose. Data
        that we collect is used only for server administration.</p>

        <p>Users wishing to make changes to the terms in the OpenVocab vocabulary
        are required to identify themselves with an OpenID. This OpenID plus optional
        metadata included by the OpenID provider (such as full name) are stored along
        with the vocabulary terms and are publicly visible to all site visitors. This information
        forms an integral part of the OpenVocab vocabulary and is placed in the public domain.</p>

        <p>This statement applies to interactions with the open.vocab.org
        Web servers. Any questions regarding the web site and the privacy
        policy can be directed to privacy@vocab.org. </p>

        <p>As is typical, we log http requests to our server. This means that
        we know the originating IP address (e.g. 18.29.0.1) of a user agent
        requesting a URL. We also know the Referer and User-Agent information
        accompanied with an HTTP request. We do not log the specific identity
        of visitors. We occasionally analyze the log files to determine which
        files are most requested and the previous site or user agent which
        prompted the request.</p>

        <p>For your information, we will log the following about you:</p>

        <table>
            <tr>
                <th>IP Address</th>
                <td><?php echo htmlspecialchars($_SERVER["REMOTE_ADDR"]); ?></td>
            </tr>
            <tr>
                <th>Referrer</th>
                <td><?php echo htmlspecialchars($_SERVER["HTTP_REFERER"]); ?></td>
            </tr>
            <tr>
                <th>User Agent</th>
                <td><?php echo htmlspecialchars($_SERVER["HTTP_USER_AGENT"]); ?></td>
            </tr>
        </table>

        <p>Our logging is passive; we do not use
        technologies such as cookies to maintain any information on users. However, we do
        use cookies to remember user identity between sessions. All such identifying information
        is stored in the cookie on your computer, we do not store any on our servers. You are
        free to delete or refuse this cookie if you do not wish your identity to be known to
        us between sessions.</p>

        <p>Logged information is kept indefinitely as admistrative and research
        material; it is not disclosed outside of open.vocab.org host site personnel.
        Aggregate (completely non-identifying) statistics generated from these
        logs may be reported as part of research results.</p>

      </div>

      <hr>
      <div id="footer" class="quiet append-bottom">
        <?php echo config_item('footer_html'); ?>
      </div>
    </div>
  </body>
</html>

