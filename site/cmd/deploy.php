<?php

$ngnEnvPath = NGN_ENV_PATH;
copy(DATA_PATH.'/docTpl/index.md', "$ngnEnvPath/ngn/README.md");
copy(DATA_PATH.'/docTpl/pm.md', "$ngnEnvPath/pm/README.md");
Dir::copy(DATA_PATH.'/docTpl', NGN_PATH.'/doc');
print `cd $ngnEnvPath/ngn && git commit -am "deploy readme" && git push origin master`;
print `cd $ngnEnvPath/pm && git commit -am "deploy readme" && git push origin master`;
