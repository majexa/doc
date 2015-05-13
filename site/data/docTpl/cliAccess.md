#CliAccess#

> Библиотека __CliAccess__ позволяет превратить php-класс или набор классов в CLI-программу
> с отображением подсказок из doc-блоков.

##Точка Cli-вызова##

^CLI — Command Line Interface

##Введение##

При разработке веб-приложений часто бывает нужно запускать те или иные функции
через командную строку (CLI). Создание для каждой такой функции отдельного файла - обычная практика.
Вот пример такого подхода, файл `/path/to/file/command.php`:

    <?php

    require 'some/path/init.php';

    $object = new Class;
    $object->runCommand();

Запуск такого файла через командную строку нужно пропускать через php-интерпретатор:
`php /path/to/file/command.php`.

Часто же количество CLI-команд в проекте растет со временем. Создание отдельный файлов под каждую из них
 не только лишняя рутинная операция, но и копирование статично прописанных путей. Кроме того одна из команд
 может вызывать другую, что так же увеличит количество статично прописанных путей, за правильностью
 которых придётся следить. Рефакторинг такие файлов в разросшейся рабочей системе, может стать реальной
 головной болью. Так что встаёт вопрос о единой точке запуска всех CLI-команд.

В этом деле нам на помощь спешат CLI-фреймворки, такие как [php-cli-tools](https://github.com/wp-cli/php-cli-tools) или
[Symfony2 Console component](http://zalas.eu/creating-parametrized-command-line-scripts-in-php-with-symfony2-console-component/).
Оба фреймворка имеют свой API для реализации таких команд. Использования этого API и есть ещё одна рутинная операция.

__CliAccess__ даёт возможность избежать этого шага и превратить уже существующий класс в CLI-программу.
Один универсальный механизм, позволяющий запускать любой публичный не статический метод класса через
командную строку, а так же автоматически отображать справку.

^Выполнение задач в консоли может послужить хорошей перемогой (как говорят наши луганские братья) для концентрации  внимания при выполнении задачи.
Консоль ограничивает нас текстовым выводом. Каждую, поступающую программисту задачу, можно свести
к текстовому вводу (CLI-команде) и текстовому выводу этой команды — результату выполнения задачи. Реализация
некоего API, которым задача должна оканчиваться. Разумеется текстовый ввод и вывод — это ограничение, которое
должно использоваться только там, где его достаточно. Для разработчика же CLI — это дешевая и достаточный
визуализация архитектуры приложения, которой иногда так не хватает.

Рассмотрим несколько примеров использования библиотеки __CliAccess__

^В командной строке будет использована утилита `run`, входящая в состав __Ngn__. Она осуществляет инициализацию
фреймворка и подключения дополнительных библиотек.

##Примеры##

###Класс с одним методом###
Создадим следующий класс:
{class DocCliExample1}
Его можно вызвать напрямую, через утилиту `run`:
{console run "(new DocCliExample1)->someCommand(1)" projects/doc}
А можно через обёртку __CliAccess__. Вызов происходит без параметров. Имя единственного публичного метода не выводится, потому что он один:
{console run "new CliAccessArgsSingle('', new DocCliExample1)" projects/doc}

Создадим файл `/usr/bin/cliExample1`:

    #!/bin/sh
    # ngn
    php ~/ngn-env/run/run.php "new CliAccessArgsSingle('$*', new DocCliExample1)" projects/doc

Теперь использование нашего примера стало короче:
{console |docCliExample1| run "new CliAccessArgsSingle('', new DocCliExample1)" projects/doc}
Вызов с одним обязательным параметром:
{console |docCliExample1 1| run "new CliAccessArgsSingle('1', new DocCliExample1)" projects/doc}
Вызов с опциональным параметром:
{console |docCliExample1 1 1| run "new CliAccessArgsSingle('1 1', new DocCliExample1)" projects/doc}

###Класс с несколькими методами###
Добавим ещё один метод.
{class DocCliExample2}
Теперь справка слегка видоизменилась:
{console |docCliExample2| run "new CliAccessArgsSingle('', new DocCliExample2)" projects/doc}
{console |docCliExample2 one 20 10| run "new CliAccessArgsSingle('one 20 10', new DocCliExample2)" projects/doc}
{console |docCliExample2 two| run "new CliAccessArgsSingle('two', new DocCliExample2)" projects/doc}

###Класс с несколькими методами и конструктором###
Теперь в классе появляется конструктор с параметрами.
{class DocCliExample3}
Так что вторым параметром в обёртку __CliAccess__
будем передавать не объект, а имя класса:

    #!/bin/sh
    # ngn
    php ~/ngn-env/run/run.php "new CliAccessArgsSingle('$*', 'DocCliExample3')" projects/doc

Вторым параметром запуска команд класса следует `name` — обязательный параметр конструктора:
{console |docCliExample3| run "new CliAccessArgsSingle('', 'DocCliExample3')" projects/doc}

Вот результат выполнения первой команды:
{console |docCliExample3 one crub 5| run "new CliAccessArgsSingle('one crub 5', 'DocCliExample3')" projects/doc}

###Несколько классов с одним префиксом###
Создадим особый вид обёртки. Она сама будет включать в себя нужный список класов:
{class DocMultiCli}

Файл запуска теперь будет выглядеть так:

    #!/bin/sh
    # ngn
    php ~/ngn-env/run/run.php "new DocMultiCli('$*')" doc

{console |docCli| run "new DocMultiCli('')" projects/doc}
^Обратите внимание. Имя команды (оливковым) генерируется автоматически из префикса (в данном случае).
Так что скрипт для запуска лучше было бы назвать именно так. Для изменения имени команды в справке
нужно переопределить метод `CliAccess::_runner()`.

###Несколько классов определенные статично###
Используем в здесь классы, созданные в предыдущих примерах.
Так же переопределим метод `CliAccess::_runner()`, что-бы назвать скрипт запуска так же.
{class DocMultiStaticCli}
{console |my-script| run "new DocMultiStaticCli('')" projects/doc}
{console |my-script bee one 321 456| run "new DocMultiStaticCli('bee one 321 456')" projects/doc}

###Класс с динамическим подклассом###
Бывает удобно расширять функционал существующей CliAccess-обёртки подкомандами.
Когда одна корневая команда может вызывать список возможных подкоманд, а этот список зависит
от параметров корневой команды.

^Все команды, что мы рассматривали до этого были корневыми, если рассматривать их в данном контексте.

{class DocDynamicSub}
Справка без указания второго параметра выглядит так же, как раньше:
{console |docDynamicSub| run "new CliAccessArgsSingle('', 'DocDynamicSub')" projects/doc}
Введём вторую команду, что бы посмотреть результат.

В первом случае выводятся подкоманды класса `DocCliExample1`
{console |docDynamicSub withSub one| run "new CliAccessArgsSingle('withSub one', 'DocDynamicSub')" projects/doc}
{console |docDynamicSub withSub one 6 7| run "new CliAccessArgsSingle('withSub one 6 7', 'DocDynamicSub')" projects/doc}

Во втором — `DocCliExample3`:
{class DocCliExample3}
{console |docDynamicSub withSub abc| run "new CliAccessArgsSingle('withSub abc', 'DocDynamicSub')" projects/doc}
Вызывается метод `DocCliExample3::one()`:
{console |docDynamicSub withSub abc one 6 7| run "new CliAccessArgsSingle('withSub abc one 6 7', 'DocDynamicSub')" projects/doc}
Вызывается метод `DocCliExample3::two()`:
{console |docDynamicSub withSub abc two| run "new CliAccessArgsSingle('withSub abc two', 'DocDynamicSub')" projects/doc}

###Класс с опциями###
Класс с опциями удобно использовать для существубщих классов, где конструктор принимает
в качестве единственного аргумента массив опций. На такие классы накладываются некоторые требования:

- Они должны наследоваться от `ArrayAccessebleOptions`
- API-методы должны иметь префикс `a_`
- Обязательные опции указываются в статическом массиве `ArrayAccessebleOptions::$requiredOptions`
- Уникальные опции в рамках API-метода указываются в doc-блоке метода

Рассметрим пример класса с опциями:
{class DocOpt}

Файл запуска будет выглядеть так:

    #!/bin/sh
    # ngn
    php ~/ngn-env/run/run.php "new CliAccessArgsSingle('$*', new DocCliExample1)" projects/doc

Подсказки:
{console |doc| run "new CliAccessOptions('', 'doc')" projects/doc}
Вызов метода:
{console |doc opt printVariant sunday is 1| run "new CliAccessOptions('opt printVariant sunday is 1', 'doc')" projects/doc}

###Вызов одного экшена у наскольких экземпляров одного класса###
Предыдущий пример можно превратить в мульти-вызов, наследуюя класс `CliAccessOptionsMultiWrapper` и реализуя его метод
 `records()`.
 
####Логика работы:####

- Допустим у Вас уже есть класс на основе `ArrayAccessebleOptions` с реализацией нужных экшенов.
 Пусть это будет класс `DocOpt` из предыдущего примера 
- Для вызова одного экшена у различных экземпляров класса `DocOpt`, нужно реализовать мульти-обёртку - класс,
 наследуемый от `CliAccessOptionsMultiWrapper`. Его имя должно иметь следующий формат: `SingleAction{s}`. В нашем случае,
 это будет `DocOpts`
- В классе нужно реализовать один метод `records`, который должен возвращать массив с обязательными
 для `DocOpt` опциями, .т.е. единственная опция `name` (`static $requiredOptions = ['name'];`).
 
 ~~~CliAccessOptions
 
 
Рассмотрим на примере:
 
{class DocOpts}

{console |doc opts printVariant sunday is 1| run "new CliAccessOptions('opts printVariant sunday is 1', 'doc')" projects/doc}
