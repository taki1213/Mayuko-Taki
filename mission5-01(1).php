<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name ="viewport" content="width=device-width,initial-scale=1.0">
    <title>mission_5-01</title>
</head>
<body>
    
<?php
//////DB接続設定////////

    $dsn = 'mysql:dbname=データベース名;host=localhost';//データベースへ接続！！ここ大事！！
    $user = 'ユーザ名';//ユーザ名
    $password = 'パスワード';//パスワード
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

    $sql_1 = "CREATE TABLE IF NOT EXISTS takitable" //テーブルを作成（CREATE構文）
        ." ("
        . "id INT AUTO_INCREMENT PRIMARY KEY," //idは自動設定かつプライマリーキーを使い識別
        . "name varchar(32)," //nameカラムは32文字以内可変で保存
        . "comment TEXT," //テキスト型
        . "date DATETIME," //時刻型
        . "pass char(16)" //16文字固定で保存
        .");";
    $stmt = $pdo->query($sql_1);

    // //テーブルごと消して再作成する
	//$sql_2 = 'truncate table msgtable';
	//$stmt = $pdo->prepare($sql_2);
	//$stmt->bindParam(':id', $id, PDO::PARAM_INT);
	//$stmt->execute();
	
    $edit_number=NULL; //変数を全て初期化する
    $edit_name=NULL;
    $edit_comment=NULL;
    $edit_pass=NULL;
    $post_date=NULL;

	
/////投稿機能//////
if(isset($_POST["name"]) && $_POST["name"] != "" &&isset($_POST["comment"]) && $_POST["comment"] != "" && isset($_POST["pass"]) && $_POST["pass"] != "" &&empty($_POST["edit_num2"])){//投稿空じゃなく名前あり、空じゃなくコメントあり、パスあり、編集番号は空の時
    //書き込み準備
    $name = $_POST["name"];
    $comment = $_POST["comment"];
    $date = date("Y/m/d/ H:i:s");
    $pass = $_POST["pass"];

    $sql = $pdo -> prepare("INSERT INTO takitable (name, comment,date,pass) VALUES (:name, :comment, :date, :pass)"); //prepare関数でインサート、実行はexecuteで行うので忘れずに
    $sql -> bindParam(':name', $name, PDO::PARAM_STR); //bindParam文字列をバインドする
    $sql -> bindParam(':comment', $comment, PDO::PARAM_STR); //上でテーブル名のそれぞれに対してパラメータを与える
    $sql -> bindValue(':date', $date, PDO::PARAM_STR);
    $sql -> bindParam(':pass', $pass, PDO::PARAM_STR);
    $sql -> execute(); //ここでprepare関数が実施される。
    }
    
/////削除機能///////
if(isset($_POST["delete"]) && $_POST["delete"] != "" && isset($_POST["pass_del"]) && $_POST["pass_del"] != "" ){
	
    $sql_5 = 'SELECT * FROM takitable'; //全体を取得
    $stmt = $pdo->query($sql_5); //$sqlに入ったクエリを$pdoに対して実行する
    $results = $stmt->fetchAll(); //全てのデータを$resultsにフェッチする
    foreach ($results as $row){
        if($row['pass']==$_POST["pass_del"] && $row['id']==$_POST["delete"]){ //パスワードとidが一致したものがある時deleteを実行
            $sql_6 = 'delete from takitable where id=:id '; //idが:idに入っている値のカラムを削除
            $stmt = $pdo->prepare($sql_6);
            $stmt->bindParam(':id', $_POST["delete"], PDO::PARAM_INT); //:idに$delete_numを挿入
            $stmt->execute();
        }
    }
}

/////////編集機能/////////
//（テーブルの書き換え）//
if(isset($_POST["name"]) && $_POST["name"] != "" && 
   isset($_POST["comment"]) && $_POST["comment"] != "" &&
   isset($_POST["pass"]) && $_POST["pass"] != "" &&
   isset($_POST["edit_num2"]) && $_POST["edit_num2"] != ""){
       
    //書き込み準備
    $id = $_POST["edit_num2"];
    $name = $_POST["name"];
    $comment = $_POST["comment"];
    $date = date("Y/m/d H:i:s");
    $pass = $_POST["pass"];
    
    //if($row['id']==$_POST["edit_num2"]){ //パスワードとidが一致したものがある時updateを実行
    $sql_9 = 'update takitable set name=:name,comment=:comment,date=:date, pass=:pass where id=:id '; //idが:idに入っている値のカラムを削除
    $stmt = $pdo->prepare($sql_9);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->bindParam(':name', $name, PDO::PARAM_STR);
    $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
    $stmt->bindParam(':date', $date, PDO::PARAM_STR);
    $stmt->bindParam(':pass', $pass, PDO::PARAM_INT); //:idに$edit_numberを挿入
    $stmt->execute();
    //}
    //$sql_7 = 'SELECT * FROM takitable'; //全体を取得する
    //$stmt = $pdo->query($sql_7); //$sqlに入ったクエリを$pdoに対して実行する
    //$results = $stmt->fetchAll(); //全てのデータを$resultsにフェッチする、fechAllとは該当する全てのデータを配列として返す
    //foreach ($results as $row){
        //if($row['pass']==$_POST["pass_edit"] && $row['id']==$_POST["edit_num2"]){ //パスワードとidが一致したものがある時deleteを実行
            //$edit_number=$row[0]; //変数を用意することでhtmlでフォーム内に埋め込む
            //$edit_name=$row[1];
            //$edit_comment=$row[2];
            //$edit_pass=$row[4];
        //}
    //}
}
//（編集する投稿の選択）//
if(isset($_POST["edit_num1"]) && $_POST["edit_num1"] != "" &&isset($_POST["pass_edit"]) && $_POST["pass_edit"] !=""){
    
    $id = $_POST["edit_num1"];
    $pass = $_POST["pass_edit"];
    
    $sql_8 = 'SELECT * FROM takitable WHERE id=:id and pass=:pass' ; //全体(*)を取得、whereで制限をつける idとpassだけで良いから
        $stmt = $pdo->prepare($sql_8); //$sqlに入ったクエリを$pdoに対して実行する、命令文をqueryに入れる
        // SQL文にユーザからの入力情報を代入
        $stmt->bindParam(':id', $id, PDO::PARAM_INT); //このカラムに入れるのは数字（整数）ですよ、少数だったら違うやつ
        $stmt -> bindParam(':pass', $pass, PDO::PARAM_STR); //このカラムに入れるのは文字です
        // ユーザからの入力情報を含んだSQLを実行
        $stmt->execute();
        
        //表示させるための準備
        $results = $stmt->fetchAll(); //全てのデータを$resultsにフェッチする、配列を用意してデータベースの中身を順番に入れてく
        foreach ($results as $row){
            $edit_num = $row['id'];
            $edit_name = $row['name'];
            $edit_comment = $row['comment'];
        }
             //if($row['id']==$_POST["edit_num1"]){ //パスワードとidが一致したものがある時updateを実行
                //$sql_9 = 'update takiable set name=:name,comment=:comment,post_date=:post_date where id=:id '; //idが:idに入っている値のカラムを削除
                //$stmt = $pdo->prepare($sql_9);
                //$stmt->bindParam(':name', $name, PDO::PARAM_STR);
                //$stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
                //$stmt->bindParam(':post_date', $post_date, PDO::PARAM_STR);
                //$stmt->bindParam(':id', $_POST["edit_num1"], PDO::PARAM_INT); //:idに$edit_numberを挿入
                //$stmt->execute();
        // }
}
?>
<!--投稿内容（名前、コメント、編集対象番号（隠す））送信フォーム-->
<form action = "" method = "post">
    <input type = "text" name = "name" placeholder = "名前" value = "<?php if(isset($edit_name)){echo $edit_name;} ?>"><br>
    <input type = "text" name = "comment" placeholder = "コメント" value = "<?php if(isset($edit_comment)){echo $edit_comment;} ?>"><br>
    <input type = "password" name = "pass" placeholder = "パスワード">
    <input type = "hidden" name = "edit_num2" placeholder = "編集対象番号" value = "<?php if(isset($edit_num)){echo $edit_num;} ?>">
    <input type = "submit" name ="submit">
</form><br>
<!--削除対象番号送信フォーム-->
<form action = "" method = "post">
        <input type = "number" name = "delete" placeholder = "削除対象番号"><br>
        <input type = "password" name = "pass_del" placeholder = "パスワード">
        <input type = "submit" name = "submit" value = "削除">
</form><br>
<!--編集対象番号送信フォーム-->
<form action = "" method ="post">
        <input type = "number" name = "edit_num1" placeholder = "編集対象番号"><br>
        <input type = "password" name = "pass_edit" placeholder = "パスワード"><br>
        <input type = "submit" name = "submit" value = "編集">
</form><br>
<?php
//ブラウザに中身を表示する
echo "【 投稿一覧 】<br>";

$sql_10 = 'SELECT * FROM takitable';
$stmt = $pdo->query($sql_10); //$sqlに入ったクエリを$pdoに対して実行する
$lines = $stmt->fetchAll();
// print_r($results);
foreach ($lines as $row){
    //$rowの中にはテーブルのカラム名が入る
    echo $row['id'].',';
    echo $row['name'].',';
    echo $row['comment'].',';
    echo $row['date'].'<br>';
echo "<hr>";
}

?>
</body>
</html>