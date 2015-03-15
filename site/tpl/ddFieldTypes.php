<?

foreach (DdFieldCore::getTypes() as $v) {
    print <<<TEXT

###{$v['title']}###
{$v['descr']}

 - Тип [элемента поля](/doc/ngn#Элемент_поля_формы): __{$v['type']}__
 - Тип поля в БД: {$v['dbType']}
 - Длина поля в БД: {$v['dbLength']}

TEXT;
}
