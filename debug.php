<?php
echo "<h1>URL Debugging Information</h1>";
echo "<p>REQUEST_URI: " . $_SERVER['REQUEST_URI'] . "</p>";
echo "<p>SCRIPT_NAME: " . $_SERVER['SCRIPT_NAME'] . "</p>";
echo "<p>PHP_SELF: " . $_SERVER['PHP_SELF'] . "</p>";
echo "<p>Current file: " . __FILE__ . "</p>";
