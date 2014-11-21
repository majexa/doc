<?php

file_put_contents(NGN_PATH.'/README.md', PreMarkdown::process(__DIR__.'/web/site/data/docTpl/tpl.ngn.md'));
print "done\n";