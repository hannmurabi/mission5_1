<!doctype html>
<html lang="ja">

<head>
<meta charset="UTF-8">
<title>5-1</title>
</head>

<body>
<?php
    // DB接続設定
    $dsn = 'データベース名';
	$user = 'ユーザー名';
	$password = 'パスワード';
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

    //4-1で書いた「// DB接続設定」のコードの下に続けて記載する。
    //テーブルを新規作成
	$sql = "CREATE TABLE IF NOT EXISTS mission5_1"
	." ("
	. "id INT AUTO_INCREMENT PRIMARY KEY,"
	. "name char(32),"
    . "comment TEXT,"
    . "date TEXT,"
    . "pass TEXT"
	.");";
    $stmt = $pdo->query($sql);

    
         /*以下のif内では、送信された編集番号とパスワードを受け取ったとき、
         パスワードが合致するときにのみ、指定された投稿の名前とコメントを
         htmlテキストエリアに表示する*/
         if(!empty($_POST["post_editnumber"] && !empty($_POST["editpass"]))){
            $editnumber=$_POST["post_editnumber"];
            //入力したデータレコードを抽出
            $sql = 'SELECT * FROM mission5_1';
            $stmt = $pdo->query($sql);
            $results = $stmt->fetchAll();
            foreach ($results as $row){
                 //$rowの中にはテーブルのカラム名が入る
                 if($editnumber==$row['id'] && $_POST["editpass"]==$row['pass'] &&
                    $row['comment']!=="（この投稿は削除されました。）"){
                    $send_editnumber=$row['id'];
                    $editname=$row['name'];
                    $editcomment=$row['comment'];
                    $txt="編集する";
                    break;
                 }else{
                    $editname="";
                    $editcomment="";
                 }
            }
        }
    
        //名前とコメントが送信されたとき
        elseif(!empty($_POST["name"]) && !empty($_POST["comment"])){
        
            //編集用パスワードが同時に送信されたとき
            if(!empty($_POST["send_editnumber"])){
                $editname=$_POST["name"];
                $editcomment=$_POST["comment"];
                date_default_timezone_set("Asia/Tokyo");
                $date=date("Y年/m月/n日　H時/i分/s秒");

                //入力したデータレコードを抽出
                 $sql = 'SELECT * FROM mission5_1';
                 $stmt = $pdo->query($sql);
                 $results = $stmt->fetchAll();
                 foreach ($results as $row){
                     //$rowの中にはテーブルのカラム名が入る
                    $id = $_POST["send_editnumber"]; //変更する投稿番号
	                $name = $editname;
                    $comment = $editcomment;
                    $date = $date;
	                $sql = 'UPDATE mission5_1 SET name=:name,comment=:comment,date=:date WHERE id=:id';
	                $stmt = $pdo->prepare($sql);
	                $stmt->bindParam(':name', $name, PDO::PARAM_STR);
                    $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
                    $stmt->bindParam(':date', $date, PDO::PARAM_STR);
	                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                    $stmt->execute(); 
                }

            
            //新規の投稿を行うとき
            }else{
                    if(!empty($_POST["postpass"])){
                        $post_pass=$_POST["postpass"];
                    }
                $post_name=$_POST["name"];
                $post_comment=$_POST["comment"];
                date_default_timezone_set("Asia/Tokyo");
                $date_create=date("Y年/m月/n日　H時/i分/s秒");
                
                //データベースに書き込み
                $sql = $pdo -> prepare("INSERT INTO mission5_1 (name, comment, date, pass) VALUES (:name, :comment, :date, :pass)");
	            $sql -> bindParam(':name', $name, PDO::PARAM_STR);
                $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
                $sql -> bindParam(':date', $date, PDO::PARAM_STR);
                $sql -> bindParam(':pass', $pass, PDO::PARAM_STR);
                $name=$post_name;
                $comment=$post_comment;
                $date=$date_create;
                $pass=$post_pass;
                $sql -> execute();
                
                

            }

        }
    
    /*以下のif内では、送信された削除番号を受け取りかつ名前とコメントが送信
    されておらず、かつパスワードが合致するとき、指定された投稿番号に該当す
    る投稿をtxtファイルから削除する*/
    if(!empty($_POST["delnumber"]) && !empty($_POST["delpass"]) && empty($_POST["name"]) &&
    empty($_POST["comment"])){
        $delnumber=$_POST["delnumber"];
        $delpass=$_POST["delpass"];

        //入力したデータレコードを抽出
        $sql = 'SELECT * FROM mission5_1';
        $stmt = $pdo->query($sql);
        $results = $stmt->fetchAll();
        foreach ($results as $row){
            //$rowの中にはテーブルのカラム名が入る
            if($delnumber==$row['id'] && $delpass==$row['pass']){
                $id = $delnumber; //変更する投稿番号
	            $name = "";
                $comment = "（この投稿は削除されました。）";
                $date="";
                $pass="";
	            $sql = 'UPDATE mission5_1 SET name=:name,comment=:comment,date=:date,pass=:pass WHERE id=:id';
	            $stmt = $pdo->prepare($sql);
	            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
                $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
                $stmt->bindParam(':date', $date, PDO::PARAM_STR);
                $stmt->bindParam(':pass', $pass, PDO::PARAM_STR);
	            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
                
            }
        }
  

    }
    

    //データベースに書き込まれている内容を表示する
    $sql = 'SELECT * FROM mission5_1';
	$stmt = $pdo->query($sql);
	$results = $stmt->fetchAll();
	foreach ($results as $row){
		//$rowの中にはテーブルのカラム名が入る
		echo $row['id'].' ';
        echo $row['name'].' ';
        echo $row['date'].' ';
        if(!empty($row['pass'])){
            echo "パスワード設定済み"."<br>";
        }else{
            echo "<br>";
        }
        echo $row['comment']."<br>";
        echo "<br>";
	    echo "<hr>";
	}

?>

<form action="" method="post">


<!--編集番号が指定されたとき、その番号を受け取る-->
<input type="hidden" name="send_editnumber" 
       value="<?php 
       if(!empty($_POST["post_editnumber"]) && !empty($_POST["editpass"])){
             echo $send_editnumber;
        }
             ?>"
>
<br>


<label>
    お名前：&emsp;
    <input type="txt" name="name" placeholder="お名前" 
           value="<?php 
           if(!empty($_POST["post_editnumber"]) && !empty($_POST["editpass"])){
               echo $editname;
           }
                    ?>"
    >
    <br>
</label>

<!--コメントの入力エリアを作成-->
<labe">
    コメント:
    <input type="txt" name="comment" placeholder="コメント"
            value="<?php
            if(!empty($_POST["post_editnumber"]) && !empty($_POST["editpass"])){
                echo $editcomment;
            }
                    ?>"
    >
    <br>
</label>

<label>
    パスワード：
    <input type="password" name="postpass" maxlength="10">
    <br>
</label> 
                    
<input type="submit" name="submit"
       value="<?php
       if(!empty($txt)){
        echo $txt;
       }else{
        echo "投稿";
       }
                ?>"
>
<br>
<br>

<!--削除番号の入力エリアを作成する-->
<label>
    削除番号：
    <input type="number" name="delnumber" min="1">
    <br>
</label>

<!--削除する際に入力するパスワードの入力エリアを作成する-->
<label>
    パスワード：
    <input type="password" name="delpass" maxlength="10">
    <br>
</label>

<!--削除番号とパスワードを送信するボタンを作成する-->
<input type="submit" name="delsubmit" value="削除">
<br>
<br>

<!--編集する投稿番号の入力エリアを作成する-->
<label>
編集対象番号:
<input type="number" min=1 name="post_editnumber">
<br>
</label>
            
<!--編集する際のパスワードを入力する-->
<label>
パスワード：
<input type="password" name="editpass" maxlength="10">
<br>
</label>
            
<input type="submit" name="editsubmit" value="編集指定">
<br>
<br>

</form>



</body>

</html>
