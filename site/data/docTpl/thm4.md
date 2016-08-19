#thm4#

Проект __htm4__ - это тема с готовым дизайном и интерфейсами для фреймворка Ngn.
Представляет собор набор шаблонов и базовых контроллеров и может использоваться как быстроек бутстрап решение для развёртывания
сайтов.

##Установка##

    pm localServer createProject projectName project-domainName.com thm4
    
В папке `thm4` находятся 2 инсталяционных скрипта.

Первый комирует базовую инициализацию из папки `thm4/dummyProject` в папку проекта.
 
Второй устанавливает обязательные модули

##Обязательные модули##
###profile###

##Собственный контроллер##

    class CtrlProjectName extends CtrlThmFourBase {
      function action_default() {
        $this->setPageTitle('Some title');
        $this->d['tpl'] = 'bookmarkContent';
        $this->d['html'] = 'yout text here';
      }
    }