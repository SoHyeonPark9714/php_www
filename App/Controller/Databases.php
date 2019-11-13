<?php
namespace App\Controller;
class Databases
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

        $query = "SHOW DATABASES;";
        $result = $this->db->queryExecute($query);

        $count = mysqli_num_rows($result);
        $rows = []; //배열 초기화
        $content = "";
        for($i=0; $i<$count; $i++){
            $row = mysqli_fetch_object($result);
            //$rows []= $row; //배열 추가
            //배열 + 배열 = 2차원 배열
            //키 값의 연상 배열
            $rows []= [
                'num'=>$i,
                'name'=>"<a href='/Tables/".$row->Database."'>".$row->Database."</a>"
            ];
        }

        $content = $html->table($rows);

        $body = file_get_contents("../Resource/database.html");
        $body = str_replace("{{content}}",$content, $body);
        echo $body;
    }
}