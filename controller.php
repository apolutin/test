<?

header("Content-Type: text/html; charset=utf-8");

require('model.php');

db_connection();

if (!isset($_POST) AND !isset($_GET)) echo $lang['error_data'];
# Записываем новый результат
if (empty($_GET) AND isset($_POST) AND !empty($_POST)):

  if (newScore_validate()):

    newScore_add();

  else: 

    echo $error;

  endif;
# Выводим результаты
elseif(isset($_GET) AND !empty($_GET)):

  if(validate_result()):

    GetDistinctName();
    GetPeriod($period);
    GetScoreByPeriod();
    ResultArray();
    Pagination();
    GetJson();

  endif;

else:

  echo $lang['error_post'];

endif;


# ФУНКЦИИ

### Запись нового результата

function db_connection(){

  $host="localhost"; $user="root"; $password=""; $db="score";

  mysql_connect($host, $user, $password); mysql_select_db($db);
}

function newScore_check_string($param){

  global $data, $error, $lang;

  if (isset($_POST[$param]) AND !empty($_POST[$param])):

    $data[$param] = htmlspecialchars($_POST[$param]);

  else:

    $error[] = $lang['error_'.$param];

  endif;

}

function newScore_check_integer($param){

  global $data, $error, $lang;

  if (isset($_POST[$param]) AND !empty($_POST[$param]) AND is_numeric($_POST[$param])):

    $data[$param] = (int)($_POST[$param]);

  else:

    $error[] = $lang['error_'.$param];

  endif;

}

function newScore_validate(){
  
  global $data, $error;

  $data = array(); $error = array();

  foreach ($_POST as $param => $value):

    if ($param == "name"):

      newScore_check_string($param);

    else:

      newScore_check_integer($param);

    endif;

  endforeach;

  if (!empty($error)):

    $error = implode('<br/>',$error);

    return FALSE;

  else:

    return TRUE;

  endif;



}

function newScore_add(){

  global $data, $lang, $newScore_add_query;

  $query = str_replace(array('%name%','%score%','%year%','%month%','%day%'),
                       array($data['name'],$data['score'],$data['year'],$data['month'],$data['day']),
                       $newScore_add_query);

  if (mysql_query($query)):

    echo str_replace('%name%', $data['name'], $lang['newScore_added']);

  else:

    echo $lang['error_mysql'];

  endif;

}

# Вывод результатов

function validate_result(){

  global $period, $page;

  if (isset($_GET['period']) AND isset($_GET['page'])):

    $period = htmlspecialchars($_GET['period']);
    $page = (int)$_GET['page'];

    return TRUE;

  else:

    return FALSE;

  endif;

}

function GetDistinctName(){

  global $json,$distinct_name_query;

  $json = array();

  $query = mysql_query($distinct_name_query);

  while ($names = mysql_fetch_array($query)):

    $json[$names[0]] = ''; 

  endwhile;

}

function GetPeriod($duration){

  global $period, $period_time;

  switch ($duration):

    case 'daily':
      $period = 'AND date_added = \''.date('Y-m-d').'\';'; $period_time = 'daily'; break;
    case 'weekly':
      $period = 'AND date_added >= \''.date('Y-m-d', time() - 7*24*60*60).'\' AND date_added <= \''.date('Y-m-d').'\';'; $period_time = 'weekly'; break;
    case 'monthly':
      $period = 'AND date_added >= \''.date('Y-m-d', time() - 30*24*60*60).'\' AND date_added <= \''.date('Y-m-d').'\';'; $period_time = 'monthly'; break;
    case 'all_time':
      $period = ''; $period_time = 'all_time'; break;

  endswitch;

  if (!isset($period_time) OR empty($period_time)):

    $period_time = 'all_time';

  endif;

}

function GetScoreByPeriod() {

  global $json, $period, $select_score_by_period_query;

  foreach ($json as $name=>$result):

    //echo str_replace(array('%name%', '%period%'),array($name,$period),$select_score_by_period_query);

    $get_sum = mysql_fetch_array(mysql_query(str_replace(array('%name%', '%period%'),array($name,$period),$select_score_by_period_query)));

    if (!empty($get_sum[0])):

      $json[$name] = $get_sum[0];

    endif;

  endforeach;



}

function ResultArray(){

  global $json;

  $json = array_filter($json);

  arsort($json);

}

function Pagination(){

  global $json, $page, $max_score_on_page, $istart, $istop;

  $max_score_on_page = 3;

  $pages = ceil(count($json)/$max_score_on_page);

  if ($page > $pages):

    $page = '1';

  elseif ($page < 1):

    $page = $pages;

  endif;

  $istop = $page * $max_score_on_page;

  $istart = $istop - $max_score_on_page + 1;



}

function GetJson(){

  global $json, $page, $max_score_on_page, $period_time, $istart, $istop;

  $i = 1; $td = 1; $json_array = array();

  foreach ($json as $param => $value):

    if ($i >= $istart AND $i <= $istop):

      $json_array['rank'.$td] = $i;
      $json_array['name'.$td] = $param;
      $json_array['score'.$td] = $value;

      $td++;

    endif;

    $i++;

  endforeach;

  $json_array['period'] = $period_time;
  $json_array['page'] = $page;

  $json = json_encode($json_array);

  print_r($json);

}

?>
