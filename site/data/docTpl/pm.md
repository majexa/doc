#pm - утилита для управления проектами#

- создание бутстрап-проекта на Ngn
- управление в-хостами
- ф-ии на уровне проекта
- обновление web-точек входа и cli-точек входа: `pm localProjects updateIndex`

{console pm}

##Полезные команды##

    # выключает режим отладки на всех проектах
    pm localProjects replaceConstant name core IS_DEBUG false
    
    # включает режим отладки на всех проектах
    pm localProjects replaceConstant name core IS_DEBUG true