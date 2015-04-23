# Результаты игры (вывод очков игроков)

[Пример реализации](http://test.polutin.ru/ "Пример")

## Создание таблицы для тестирования

`CREATE TABLE score_table (id int auto_increment primary key, name VARCHAR (100), score INTEGER (10), date_added datetime);`

## Описание файлов

### index.php
Вывод фреймов
### style.css
Каскадная таблица стилей
### form.htm
Фрейм с формой ввода нового результат
### results.htm
Фрейм с таблицей вывода ранее загруженных результатов
### controller.php
Логика серверной части + методы
### model.php
Тексты ошибок, запросы к БД
