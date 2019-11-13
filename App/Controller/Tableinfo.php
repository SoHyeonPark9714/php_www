<?php
namespace App\Controller;
class TableInfo
{
    private $db;
    // 생성자
    public function __construct($db)
    {
        //echo __CLASS__;
        $this->db = $db;
    }

    public function main()
    {
        $html = new \Module\HTML\HTMLTable;

        $uri = $_SERVER['REQUEST_URI'];
        $uris = explode("/",$uri);

        //echo "메인 호출";
        $query = "DESC ".$uris[2];
        $result = $this->db->queryExecute($query);

        $count = mysqli_num_rows($result);
        $rows = []; //배열 초기화
        $content = "";
        for($i=0; $i<$count; $i++){
            $row = mysqli_fetch_object($result);
            $rows []= $row; //배열 추가
        }
        $content = $html->table($rows);

        $body = file_get_contents("../Resource/desc.html");
        $body = str_replace("{{content}}",$content, $body);
        echo $body;
    }
}