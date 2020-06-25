<?php
if (isset($_GET['user']) and $_GET['user']=='expert' and (!isset($_POST['one']) )) {
    echo '<form method="POST" action="?user=expert">
    <label for="one">2+2=<input type="number" id="one" name="one" value="0"  step="1"></label>
    <label for="two">2*3=<input type="number" id="two" name="two" value="0" min=0 step="1"></label>
    <label for="three">Столица Великобритании:<input type="text" id="three" name="three" maxlength=30></label>
    <label for="four">Что вы думаете о Московском политехе?:
                <textarea id="four" name="four" maxlength=255></textarea>
            </label>
            Кошка это земноводное?<label class="radio_inp" for="yes">Да
<input type="radio" class="radio_inp" value="да" name="five" id ="yes"/></label> <!-- value это значение в пост name это ключ в пост id это значение для label -->
<label class="radio_inp" for="no">Нет
<input type="radio" class="radio_inp" value="нет" name="five" id="no"/><br></label>
            
            <label for="six">Беспозвоночные животные это:
                <select multiple="multiple" id="six" name="six[]">
                    <option value="Черви">Черви</option>
                    <option value="Рыбы">Рыбы</option>
                    <option value="Пауки">Пауки</option>
                    <option value="Маллюски">Маллюски</option>
                </select>
            </label>
            <input type="submit" value="Отправить">
        </form>';
}
if (isset($_POST['one']))
{
    $connect = pg_connect("host=localhost port=5432 dbname=phpexamen user=postgres password=fly2505")
 or die('Не удалось соединиться: '.pg_last_error($connect));
// if( !$connect ) // если при подключении к серверу произошла ошибка
// {
// exit();
// }
$sel = '';
foreach ($_POST['six'] as $val) {
 $sel .= $val;
 $sel.=', ';
}
$ball=0;
if ($_POST['five']=='нет') $ball=$ball+25;
else $ball=$ball-25;
for($i=0; $i<=count($_POST['five']); $i++){
if ($_POST['five[i]']=='Черви)') 
$ball=$ball+25;
if ($_POST['five[i]']=='Пауки')
$ball=$ball+25;
if ($_POST['five[i]']=='Маллюски')
$ball=$ball+25;
if ($_POST['five[i]']=='Рыбы')
$ball=$ball-25;  }
$query_add=pg_query($connect, "INSERT INTO phpschema.answers(one,two,three,four,five,six,dat,tim,ip,ball) VALUES ('".
 htmlspecialchars($_POST['one'])."', '".
 htmlspecialchars($_POST['two'])."', '".
 htmlspecialchars($_POST['three'])."', '".
 htmlspecialchars($_POST['four'])."', '".
 htmlspecialchars($_POST['five'])."', '".
 htmlspecialchars($sel)."', '".
 htmlspecialchars(date('d.m.Y'))."', '".
 htmlspecialchars(date('H:i:s'))."', '".
 htmlspecialchars($_SERVER['REMOTE_ADDR'])."', ".
 $ball.") ");
}
if($query_add and $_GET['user']=='expert')
 echo '<div class="error">Ответ записан</div>';
 else if ($query_add and $_GET['user']=='expert' and isset($_POST['one']))// если все прошло нормально – выводим сообщение
 echo '<div class="ok">Ответ не записан</div>';

 if (isset($_GET['user']) and $_GET['user']=='admin') {
     if (!isset($_POST['password']) and !isset($_GET['doing'])) {
         echo '<form method="POST" action="/?user=admin">
     <label for="pass">Введите пароль:<input type="password" id="pass" name="password"></label>
     <input type="submit" value="Отправить"></form>';
     } else if (isset($_POST['password']) and $_POST['password']!='12345') {
         echo 'Введен неправильный пароль, повторите попытку!';
         echo '<br><form method="POST" action="/?user=admin">
     <label for="pass">Введите пароль:<input type="password" id="pass" name="password"></label>
     <input type="submit" value="Отправить"></form>';
     }
     if ((isset($_POST['password']) and $_POST['password']=='12345') or isset($_GET['doing'])) {
         $connect = pg_connect("host=localhost port=5432 dbname=phpexamen user=postgres password=fly2505")
 or die('Не удалось соединиться: '.pg_last_error($connect));

         $query_read=pg_query($connect, 'SELECT one,two,three,four,five,six,dat,tim,ip,ball FROM phpschema.answers');
         while ($row = pg_fetch_array($query_read, null, PGSQL_ASSOC)) {
             $schet++;
         }
         echo '<h2><a href="/?user=admin&doing='.$schet.'">Просмотреть протокол</a></h2>';
         echo '<h2><a href="/?user=admin&doing=del">Удалить данные</a></h2>';
         if ($_GET['doing']==$schet) {
             $connect = pg_connect("host=localhost port=5432 dbname=phpexamen user=postgres password=fly2505");
             $query_read=pg_query($connect, 'SELECT one,two,three,four,five,six,dat,tim,ip,ball FROM phpschema.answers'); //выполнить запрос
             $ret='<table><tr><td>2+2=</td><td>2*3=</td><td>Столица Великобритании</td>
<td>Что вы думаете о Московском политехе?</td><td>Кошка это земноводное?</td>
<td>Беспозвоночные животные это:</td><td>дата</td><td>время</td><td>ip</td><td>баллы</td></tr>';
             while ($row = pg_fetch_array($query_read, null, PGSQL_ASSOC)) { // пока роу не 0
                 $ret.='<tr><td>'.$row['one'].'</td>
<td>'.$row['two'].'</td>
 <td>'.$row['three'].'</td>
 <td>'.$row['four'].'</td>
 <td>'.$row['five'].'</td>
 <td>'.$row['six'].'</td>
 <td>'.$row['dat'].'</td>
 <td>'.$row['tim'].'</td>
 <td>'.$row['ip'].'</td>
 <td>'.$row['ball'].'</td></tr>';
             }
             $ret.='</table>';
             $query_avg=pg_query($connect, 'SELECT avg(ball) FROM phpschema.answers');
             $avgball=pg_fetch_row($query_avg);
             echo $ret;
             echo '<br>Средний балл:'.round($avgball[0], 3);
         }
          if( $_GET['doing']=='del')
{
// значит в цикле сейчас текущая запись
$query_edit=pg_query($connect, "DELETE FROM phpschema.answers WHERE id_ans > 0");
echo 'Записи удалены';
}
     }
 }
 ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Document</title>
</head>

<body>
    <?php if (!isset($_GET['user']))
    echo '<h1>Добро пожаловать!</h1>
    <h2><a href="/?user=admin">Я администратор</a></h2>
    <h2><a href="/?user=expert">Я эксперт</a></h2>'; 
    ?>
</body>

</html>