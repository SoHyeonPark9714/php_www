<?php

$config = include "../dbconf.php";
echo "대림대학교";
print_r($config);
//같은 경로를 지정해주지 않고 웹사이트상에서 사용자가
//접속을 하지 못하게 한다.
require "../Loading.php";
/*
require "../Module/Database/Database.php";
require "../Module/Database/table.php"; 
*/
    
$db = new Database($config);
$table = new Table($db);
echo "<br>";
$query = "SHOW TABLES";
$result = $db->queryExecute($query);


$count = mysqli_num_rows($result);
for($i=0;$i<$count;$i++){
    $row = mysqli_fetch_object($result);
    echo $row->Tables_in_php."<br>";
}