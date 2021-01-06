<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>mission_5</title>
</head>
<body>
    <?php
    //データベースへ接続するための設定
    $dsn="mysql:dbname=**;host=**";
    $usurname="**";
    $password="**";
    $pdo = new PDO($dsn,$usurname,$password,array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
    //テーブルがないなら作成する
    $sql = "CREATE TABLE IF NOT EXISTS tbmi51 (
	id INT AUTO_INCREMENT PRIMARY KEY,
	name CHAR(32),
	comment TEXT,
	date DATETIME,
	password CHAR(64)
	)";
	//ここからどのボタンが押されたかでどの処理を行うかの判断
	/*
	送信ボタンが押された場合、
	新規投稿か編集なのか判断し、
	フォームからの入力内容が足りているなら
	新規投稿は名前、コメント内容、日時、（パスワード）をデータベースに追加、
	編集は名前、コメント、日時を上書き、パスワードも入力されているなら上書き。
	エラーがあるならそれぞれエラー内容をエラー用の変数に入力。
	*/
$er = "";
    if(!empty($_POST["sousin"])){
        //それが編集しに来たものか判断
        if(empty($_POST["hanntei"])){
            //新規投稿なら名前とコメントが入力されているかの判断
            if(!empty($_POST["name"])&&!empty($_POST["str"])){
                //データベースに新しく追加
                $sql = $pdo -> prepare("INSERT INTO tbmi51 (name, comment, date, password) VALUES (:name, :comment, :date, :password)");
                $sql -> bindParam(':name', $name, PDO::PARAM_STR);
                $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
                $sql -> bindParam(':date', $date, PDO::PARAM_STR);
                $sql -> bindParam(':password', $password, PDO::PARAM_STR);
                $date=date("Y/m/d H:i:s");
                $name = $_POST["name"];
                $comment = $_POST["str"]; 
                $password = $_POST["spass"];
                $sql -> execute();
            }
        }else{
            //編集なら、、同じく名前とコメントが入力されているか判断
            if(!empty($_POST["name"])&&!empty($_POST["str"])){
                //名前とコメントについて上書き
                $id = $_POST["hanntei"];
                $name = $_POST["name"];
                $comment = $_POST["str"]; 
                $date=date("Y/m/d H:i:s");
                $sql = 'UPDATE tbmi51 SET name=:name,comment=:comment,date=:date WHERE id=:id';
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':name', $name, PDO::PARAM_STR);
                $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
                $stmt-> bindParam(':date', $date, PDO::PARAM_STR);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
                //パスワードも入力されているなら上書き
                if(!empty($_POST["spass"])){
                    $id = $_POST["hanntei"];
                    $password = $_POST["spass"];
                    $sql = 'UPDATE tbmi51 set password=:password where id=:id';
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindParam(':password', $password, PDO::PARAM_STR);
                    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                    $stmt->execute(); 
                }
            }
        }
        //エラーあるか判断。
        if(empty($_POST["name"])){
            $er=$er."<>"."name";
        }
        if(empty($_POST["str"])){
            $er=$er."<>"."str";
        }
    }
    /*
    削除のボタンが押されたなら、
    指定番号の投稿を削除する
    エラーがあるならそれぞれエラー内容をエラー用の変数に入力。
    */
    if(!empty($_POST["sakujo"])){
        //削除指定番号が入力されているか判断
        if(isset($_POST["kesu"])&&$_POST["kesu"]!=""){
            //パスワードが入力されているか判断
            if(!empty($_POST["kpass"])){
                //データベースから投稿を読み込んでくる
                $sql = 'SELECT id,password FROM tbmi51';
                $stmt = $pdo->query($sql);
                $results = $stmt->fetchAll();
                //一行ずつ処理していく
                foreach ($results as $row){
                        //指定番号と同じ行か判断し同じものがあれば記憶
                        if($row['id']==$_POST["kesu"]){
                            $okaru = 1;
                            //パスワードが合っているか判断し、合っていれば記憶
                            if($row['password']==$_POST["kpass"]||"kannrisya"==$_POST["kpass"]){
                                $okpass=1;
                                //データベースから投稿を削除する。
                                $id = $_POST["kesu"];
                                $sql = 'delete from tbmi51 where id=:id';
                                $stmt = $pdo->prepare($sql);
                                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                                $stmt->execute();
                            }
                        }
                }
                //エラーがあるか判断。
                if($okaru!="1"){
                    $er=$er."<>"."kesumiss";
                }
                if($okpass!="1"){
                    $er=$er."<>"."passmiss";
                }
            }else{
                $er=$er."<>"."pass";
            }
        }
        if(empty($_POST["kesu"])){
            $er=$er."<>"."kesu";
        }
    }
    /*
    編集のボタンが押されたなら、
    データベースから指定番号と同じ投稿番号のデータがあるか検索し、
    指定番号の投稿内容から名前とコメント番号をフォーム用変数に代入、
    エラーがあるならそれぞれエラー内容をエラー用の変数に入力。
    */
    if(!empty($_POST["hennsyuu"])){
        //編集指定番号があるか判断
        if(isset($_POST["kaeru"])&&$_POST["kaeru"]!=""){
                //パスワードが入力されているか判断
                if(!empty($_POST["hpass"])){
                    //データベースから読み込んでくる
                    $sql = 'SELECT * FROM tbmi51';
                    $stmt = $pdo->query($sql);
                    $results = $stmt->fetchAll();
                    //一行ずつ処理する
                    foreach ($results as $row){
                        //指定番号と同じ行か判断し同じものがあれば記憶
                        if($row['id']==$_POST["kaeru"]){
                            $okaru = 1;
                            //パスワードが合っているか判断し、合っていれば記憶
                            if($row['password']==$_POST["hpass"]||"kannrisya"==$_POST["kpass"]){
                                $okpass=1;
                                //データベースから読み込んできたものから名前とコメントを記録
                                $d1 = $row['name'];
                                $d2 = $row['comment'];
                                $d3 = $row['id'];
                            }
                        }
                    }
                    //エラーがあるか判断
                    if($okaru!="1"){
                        $er=$er."<>"."kaerumiss";
                    }
                    if($okpass!="1"){
                        $er=$er."<>"."passmiss";
                    }
                }else{
                    $er=$er."<>"."pass";
                }
            }
            if(empty($_POST["kaeru"])){
                $er=$er."<>"."kaeru";
            }
    }
    ?>
    <div>---この掲示板のテーマ---</div>
    <span style="font-size:25px;">今気に入ってる曲とかある？</span>
    <div>---------------------------</div>
    <form action=""method="post">
        <table>
            <tr><td colspan="2">---入力フォーム---</td></tr>
            <tr><td><label>名前：</label></td><td><input type="text"name="name"placeholder="名前"value="<?php if(!empty($d1)){echo $d1;} ?>"></td></tr>
            <tr><td><label>コメント：</label></td><td><input type="text"name="str"placeholder="コメント"value="<?php if(!empty($d12)){echo $d2;} ?>"></td></tr>
            <input type="hidden"name="hanntei"placeholder="編集かな？"value="<?php if(!empty($d3)){echo $d3;} ?>">
            <tr><td><label>パスワード：</label></td><td><input type="password"name="spass"placeholder="パスワード"></td></tr>
            <tr><td colspan="2"><input type="submit" name="sousin"value="送信"></td></tr>
            <tr><td colspan="2">---削除番号指定用フォーム---</td></tr>
            <tr><td><label>削除対象番号：</label></td><td><input type="number"name="kesu"placeholder="削除対象番号"min="1"step=""></td></tr>
            <tr><td><label>パスワード：</label></td><td><input type="password"name="kpass"placeholder="パスワード"></td></tr>
            <tr><td colspan="2"><input type="submit" name="sakujo"value="削除"></td></tr>
            <tr><td colspan="2">---編集番号指定用フォーム---</td></tr>
            <tr><td><label>編集対象番号：</label></td><td><input type="number"name="kaeru"placeholder="編集対象番号"min="1"step=""></td></tr>
            <tr><td><label>パスワード：</label></td><td><input type="password"name="hpass"placeholder="パスワード"></td></tr>
            <tr><td colspan="2"><input type="submit" name="hennsyuu"value="編集"></td></tr>
        </table>
    </form>
    <?php
    //エラーがあるか判断
    if(!empty($er)){
        //エラー内容を羅列、それぞれに対応する文章の出力
        $ers=explode("<>",$er);
        foreach($ers as $e){
            if($e=="name"){
                echo "////////////////////////////////"."<br>";
                echo "名前が入力されていません"."<br>";
                echo "////////////////////////////////"."<br>";
            }
            if($e=="str"){
                echo "////////////////////////////////"."<br>";
                echo "コメントが入力されていません"."<br>";
                echo "////////////////////////////////"."<br>";
            }
            if($e=="kesu"){
                echo "////////////////////////////////"."<br>";
                echo "削除対象番号が入力されていません"."<br>";
                echo "////////////////////////////////"."<br>";
            }
            if($e=="kesumiss"){
                echo "////////////////////////////////"."<br>";
                echo "削除対象番号が間違っています"."<br>";
                echo "////////////////////////////////"."<br>";
            }
            if($e=="kaeru"){
                echo "////////////////////////////////"."<br>";
                echo "編集対象番号が入力されていません"."<br>";
                echo "////////////////////////////////"."<br>";
            }
            if($e=="kaerumiss"){
                echo "////////////////////////////////"."<br>";
                echo "編集対象番号が間違っています"."<br>";
                echo "////////////////////////////////"."<br>";
            }
            if($e=="pass"){
                echo "////////////////////////////////"."<br>";
                echo "パスワードが入力されていません"."<br>";
                echo "////////////////////////////////"."<br>";
            }
            if($e=="passmiss"){
                echo "////////////////////////////////"."<br>";
                echo "パスワードが間違っています"."<br>";
                echo "////////////////////////////////"."<br>";
            }
        }
    }
    echo "<hr width=300 align='left' >";
    //投稿内容をデータベースから読み込み、表示させる
    echo "[投稿一覧]"."<br>";
    $sql = 'SELECT * FROM tbmi51';
	$stmt = $pdo->query($sql);
	$results = $stmt->fetchAll();
	foreach ($results as $row){
		echo $row['id'].',';
		echo $row['name'].',';
		echo "【";
		echo $row['comment'];
		echo "】";
		echo $row['date'].'<br>';
	
	}
	echo "<hr width=300 align='left'>";
    ?>
</body>
</html>