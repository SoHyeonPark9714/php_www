<?php
//함수, 상수를 지정
define("START", microtime(true));


$config = include "../dbconf.php";
require "../Loading.php";

// 세션 활성화
session_start();


$uri = $_SERVER['REQUEST_URI'];
$uris = explode("/",$uri); // 파란책
// print_r($uris);

$db = new \Module\Database\Database( $config );

if(isset($uris[1]) && $uris[1]) {
    // 컨트롤러 실행...
    // echo $uris[1]."컨트롤러 실행...";
    $controllerName = "\App\Controller\\" . ucfirst($uris[1]);
    $tables = new $controllerName ($db);
    
    // 클래스의 메인이 처음으로 동작하는 것로 정해요.
    //호출.
    $tables->main();

} else {
    //M(model:database)
    // 처음 페이지 에요.
    // echo "처음 페이지 에요.";
    $body = file_get_contents("../Resource/index.html");

    if($_SESSION["email"]) {
        // 로그 상태 입니다.
        $body = str_replace("{{Login}}","로그인 상태입니다. <a href='logout'>로그아웃</a>",$body);
    } else {
        // 로그인 해주세요.
        $loginForm = file_get_contents("../Resource/login.html");
        $body = str_replace("{{Login}}",$loginForm,$body);
    }
    echo $body;
}

// $desc = new \App\Controller\TableInfo;
// $desc->main();


function shutdown(){
    // echo "시작시간=".START."<br>";
    $endtime = microtime(true);
    // echo "종료시간=".$endtime;

    $running  = $endtime- START;
    echo "실행시간:".$running;
    //시작 시간과 종료시간을 정해주면 이렇게 정해주면 찍어서 차이값을 구하면 얼마나 걸리는지 알수 있다.
}
// echo shutdown();
//프로그램이 종료되면,자동으로 shutdown 함수를 호출해 줍니다.
register_shutdown_function("shutdown");

// CONST ENDTIME = microtime(true);

