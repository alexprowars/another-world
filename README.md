# Online RPG Another World

## Системные требования:
- PHP 5.6 и выше
- Phalcon 2.1 и выше
- gulp

**Рекомендуется использовать связку Nginx + php-fpm**

## Установка
1. Скомпилировать и установить PHP расширение **Phalcon** (**https://phalconphp.com/ru/download**)
2. Настроить Nginx (**install/nginx.conf**)
3. Залить скрипты игры на сервер
4. Залить БД (**install/db.sql**)
5. Переименовать конфиг **app/config/sample.config.ini** в **config.ini**
6. Внести параметры подключения к БД в файле **app/config/config.ini**
7. Установить NodeJS
8. Перейти в корневой каталог проекта и установить зависимости (**npm install**)
9. Скомпилировать стили (**gulp**)
10. Вы великолепны!