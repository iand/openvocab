<p class="error">We encountered a problem and we could not <?php echo htmlentities($goal); ?></p>
<p>The HTTP request sent was:</p>
<pre><?php echo htmlspecialchars($response->request->to_string());?> </pre>
<p>The server response was:</p>
<pre><?php echo htmlspecialchars($response->to_string()); ?></pre>

