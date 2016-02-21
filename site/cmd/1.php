<?php

$c = file_get_contents(NGN_ENV_PATH.'/ci/Ci.class.php');
ClassCore::getDocComment($c);
