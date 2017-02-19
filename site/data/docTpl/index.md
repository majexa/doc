#Фреймфорк Ngn и дополнительные пакеты входящие в состав Ngn-env#

Ngn. The fullstack client-server web-framework

##Установка на Ubuntu/Debian

    # не забудьте сменить пароль your_password_here
    wget --no-check-certificate -O - https://raw.githubusercontent.com/majexa/sman/master/web/run.sh | sed -e 's/CHANGE_PASS/"your_password_here"/g' | bash
    
После установки Вас попросят ввести 2 значения: базовый домен и email администратора.

Базовый домен должен вести на IP адрес сервера, а email используется для отправки отчётов при тестировании среды.

<!--^ Если у вас ещё нет своего домена, то вы можете воспользоваться [нашим](http://sman.majexa.ru/install-domain.php).-->

##[Ngn. Server-Side](/doc/ngn.md)##
##[Ngn. Client-Side](/doc/clientSide)##

##Утилиты Ngn-env##
Ngn — это не только серверный фреймворк, но и набор компонентов для разработки, администрирования и тестирования проектов.

В его состав входит набор утилит, помогающих облегчить эти операции:
###[run](/doc/run), [ci](/doc/ci), [pm](/doc/pm), [tst](/doc/tst), [sman](/doc/sman)###

##[Деплой](/doc/deploy)##
##[Документирование](/doc/doc)##
##[thm4](/doc/thm4)##