#Деплой#

На момент, когда в все тесты пройдены на тестовом сервере
пора делать релиз. Релиз должен сопровождаться комитом, который будет
содержать собранные JS и CSS файлы. Сборка происходит автоматически,
после чего так же автоматически проходят все те же Client-Side тесты.
После их успешного прохождения происходит авто-комит и авто-деплой
на все продакш сервера.

Теперь всё по пунктам:

    ci release
      ci update
        - Updates from git
        - Runs server-side tests
        - Runs client-side tests
      ci build - Turns off build mode
        ci cst - Runs client-side tests
          if ok git push
      ci deploy - Runs `ci update` on production servers
