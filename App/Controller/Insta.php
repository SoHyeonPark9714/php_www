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
        }else if($second == "delete"){
            $this->delete($id);
        }else if($second == "edit"){
            $this->edit($id);
        }else {
            // 목록
            $this->insta();
        }       
    }
    private function detailView($id)
    {
         $query = "SELECT * from insta WHERE id = ".$id;
        // echo $query;
        $result = $this->db->queryExecute($query);
        $data = mysqli_fetch_object($result);
        // print_r($data);
        $body = file_get_contents("../Resource/insta_view.html");
        $body = str_replace("{{username}}",$data->username."<br>", $body); // 데이터 치환
        $body = str_replace("{{images}}","<img src='/images/".$data->images."' width='100%'><br>", $body); // 데이터 치환
        $body = str_replace("{{date}}",$data->date, $body);
        $body = str_replace("{{contents}}",$data->contents."<br>", $body); // 데이터 치환
        $body = str_replace("{{id}}",$data->id."<br>", $body);
        $body = str_replace("{{delete}}","./delete/".$data->id,$body);
        $body = str_replace("{{edit}}","./edit/".$data->id,$body);
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
            if ($i%1 == 0) {
                $content .= "</div>
                <div class=\"row\">
                ";
            }
            
            $link = $_SERVER['REQUEST_URI']."/".$row->id;
            $content .= "<div class=\"col-sm\">";
            $content .="<div><a href='$link'>".$row->username."</a>(".$row->click.")</div><br>";
            $content .="<div><a href='$link'><img src='/images/".$row->images."' width='40%'/></a></div>";
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
    private function delete($id)
    {
        $third = $this->HttpUri->third();
        echo $third." 삭제합니다.";
        // 삭제쿼리
        $query = "DELETE FROM insta ";
        // 조건
        $query .= "WHERE id='".$third."'";
        echo $query; // 쿼리 확인 습관.
        $result = $this->db->queryExecute($query);
        // 페이지 이동
        header("location:"."/insta/");
    }
    private function edit($id)
    {
        // print_r($_POST);
        if ($_POST) {
            $query = "UPDATE insta SET ";
            // 갱신 데이터
            // $query .= "`FirstName`= '".$_POST['FirstName']."', ";
            // $query .= "`LastName`= '".$_POST['LastName']."' ";
            foreach ($_POST as $key => $value) {
                if($key == "id") continue;
                $query .= "`$key`= '".$value."',";
            }
            
            $query = rtrim($query, ","); // 마지막 콤마 제거
            // echo $query;
            // 조건값
            $query .= " WHERE id='".$id."'";
            // echo $query;
            // exit;
            $result = $this->db->queryExecute($query);
            // 페이지 이동
            // header("location:"."/select/".$tableName);
        }
        // step1. 데이터 조회
        $query = "SELECT * from insta WHERE id = ".$id;
        // echo $query;
        $result = $this->db->queryExecute($query);
        $data = mysqli_fetch_object($result);
        // print_r($data);
        $content = "<form method=\"post\">";
        $content .= "<input type=\"hidden\" name=\"id\" value='$id'>";
        $content .= "<input type=\"hidden\" name=\"date\" value='$date'>";
        $content .= "<input type=\"hidden\" name=\"images\" value='$images'>";
        // $content .= "<input type=\"text\" name=\"lastname\">";
        $query = "DESC insta";
        $result = $this->db->queryExecute($query);
        $count = mysqli_num_rows($result);
        for ($i=0;$i<$count;$i++) {
            $row = mysqli_fetch_object($result);
            // $rows []= $row; // 배열 추가 (2차원 배열)
            // $row = 객체
            // print_r($row);
            if($row->Field == "id") continue;
            
            // 필드명 키
            $key = $row->Field;
            
            $content .= $row->Field." <input type=\"text\" 
            name=\"".$row->Field."\" 
            value='".$data->$key."'>";
            $content .= "<br>";
        }
        
        $content .= "<input type=\"submit\" value=\"수정\">";
        $content .= "</form>";
        
        $body = file_get_contents("../Resource/insta_edit.html");
        $body = str_replace("{{content}}",$content, $body); // 데이터 치환
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