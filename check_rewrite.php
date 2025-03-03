<?php
echo "mod_rewrite is " . (in_array('mod_rewrite', apache_get_modules()) ? "enabled" : "not enabled");
