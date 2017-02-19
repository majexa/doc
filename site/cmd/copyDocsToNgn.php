<?php

// в какой момент копировать?
// в момент пуша проекта док. doc


copy(DATA_PATH.'/docTpl/index.md', NGN_PATH.'/README.md');
Dir::copy(DATA_PATH.'/docTpl', NGN_PATH.'/doc');