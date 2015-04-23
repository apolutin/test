<?

header("Content-Type: text/html; charset=utf-8");

$lang = array(
 'error_data'=>'Доступ запрещен',
 'error_post'=>'Передан пустой массив',
 'error_name'=>'Ошибка ввода имени',
 'error_score'=>'Ошибка ввода числа очков',
 'error_day'=>'Ошибка ввода дня',
 'error_month'=>'Ошибка ввода месяца',
 'error_year'=>'Ошибка ввода года',
 'error_mysql'=>'Ошибка записи',
 'newScore_added'=>'Добавлена запись для игрока %name%',
 ''=>'',
);

$newScore_add_query = "INSERT INTO  `score_table` (
`id` ,
`name` ,
`score` ,
`date_added`
)
VALUES (
NULL ,
'%name%',
'%score%',
'%year%-%month%-%day% 00:00:00'
);";

$distinct_name_query = "SELECT DISTINCT(name) FROM `score_table`";

$select_score_by_period_query = "SELECT SUM(score) FROM `score_table` WHERE name = '%name%' %period%;";

?>
