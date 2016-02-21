<?php

function get($path) {
  return (new NgnMarkdown)->markdown(file_get_contents(PROJECT_PATH.'/data/docTpl/'.$path.'.md'));
}

file_put_contents(NGN_PATH.'/README.md', get('index'));
file_put_contents(NGN_PATH.'/more/lib/sflm/README.md', get('sflm'));
print "done.\n";

// pre-markdown
// markdown-meta
// markdown
// html
