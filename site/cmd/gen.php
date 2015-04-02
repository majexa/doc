<?php

file_put_contents(NGN_PATH.'/README.md', PreMarkdown::process(PROJECT_PATH.'/data/docTpl/ngn.md'));
print "done.\n";
