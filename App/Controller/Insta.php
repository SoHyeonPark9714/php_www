<?php
namespace App\Controller;
class Insta extends Controller
{
    private $db;
    private $HttpUri;
    public function __construct($db)
    {
        $this->db = $db;
        $this->HttpUri = new \Module\Http\Uri(); // 객체생성
    }
    public function main()
    {
        $second = $this->HttpUri->second();
        if($second == "new") {
            // 데이터삽입
            $this->newInsert();
        } else if(is_numeric($second)) {
            $this->detailView($second);
        } else {
            // 목록
            $this->insta();
        }        
    }
    private function detailView($id)
    {
         $query = "SELECT * from insta WHERE id = ".$id;
        echo $query;
        $result = $this->db->queryExecute($query);
        $data = mysqli_fetch_object($result);
        // print_r($data);
        $body = file_get_contents("../Resource/goods_view.html");
        $body = str_replace("{{goodname}}",$data->goodname, $body); // 데이터 치환
        $body = str_replace("{{images}}","<img src='/images/".$data->images."' width='100%'>", $body); // 데이터 치환
        $body = str_replace("{{price}}",$data->price, $body); // 데이터 치환
        $body = str_replace("{{date}}",$data->date, $body);
        $body = str_replace("{{id}}",$data->id, $body);
        echo $body;
        $query = "UPDATE insta SET `click`=`click`+1 where id='$id'";
        $result = $this->db->queryExecute($query);
    }
    private function newInsert()
    {  $today = date('Ymd');
        if ($_POST) {
            \move_uploaded_file($_FILES['images']['tmp_name'], "images/".$_FILES['images']['name']);
            $query = "INSERT INTO insta (username,images,contents,date)
            VALUES ('".$_POST['username']."','".$_FILES['images']['name']."','".$_POST['contents']."',$today)";
            echo $query;
            $result = $this->db->queryExecute($query);
        } else {
            // echo "데이터 삽입";
            $body = file_get_contents("../Resource/insta_new.html");
            $body = str_replace("{{content}}",$content, $body); // 데이터 치환
            echo $body;
        }
    }
    private function insta()
    {
        $query = "SELECT * from insta order by click desc;";
        $result = $this->db->queryExecute($query);
        $count = mysqli_num_rows($result);
        $content = "<div class=\"container\">
        <div class=\"row\">"; //초기화
        for ($i=0,$j=1;$i<$count;$i++,$j++) {
            $row = mysqli_fetch_object($result);
            // print_r($row);
            if ($i%3 == 0) {
                $content .= "</div>
                <div class=\"row\">
                ";
            }
            
            $link = $_SERVER['REQUEST_URI']."/".$row->id;
            $content .= "<div class=\"col-sm\">";
            $content .="<div>".$row->username."</div>";
            $content .="<div><a href='$link'><img src='/images/".$row->images."' width='50%' /></a></div>";
            $content .="<div>".$row->contents."</div>";
            $content .="<div>".$row->date."</div>";
            $content .= "</div>";
        }
        $content .= "</div>
        </div>";
        // MVC 패턴에서 view 화면 분리.
        $body = file_get_contents("../Resource/insta.html");
        $body = str_replace("{{content}}",$content, $body); // 데이터 치환
        // $body = str_replace("{{category}}",$this->cate(), $body);
        // 테이블 별로 new 버튼 링크 생성
        $body = str_replace("{{new}}","/insta/new", $body);
        echo $body;
    }
   
    // private function cate()
    // {
    //     $query = "SELECT * from category";
    //     $result = $this->db->queryExecute($query);
    //     $count = mysqli_num_rows($result);
    //     $cate = "";
    //     for ($i=0,$j=1;$i<$count;$i++,$j++) {
    //         $row = mysqli_fetch_object($result);
    //         // print_r($row);
    //         $cate .= "<a href=\"#\" class=\"list-group-item\">".$row->cate."</a>";
    //     }
    //     return $cate;
    // }
}