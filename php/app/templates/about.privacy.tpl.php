<h1>OpenVocab Privacy Policy</h1>
<p class="summary">We make no effort to identify public users of our site. No 
identifying data is disclosed to any third party for any purpose. Data
that we collect is used only for server administration.</p>

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
