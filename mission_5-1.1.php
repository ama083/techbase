<?php

//データベースに接続
$dsn = 'データベース名';
	$user = 'ユーザー名';
	$password = 'パスワード';
	$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

//テーブルをつくる
$sql = "CREATE TABLE IF NOT EXISTS mission"
	." ("
	. "id INT AUTO_INCREMENT PRIMARY KEY,"
	. "name char(32),"
	. "comment TEXT,"
	. "time char(32),"	//入力時間がフォームにあったので
	. "pass char(32)"	//パスワードがフォームにあるので
	.");";
	$stmt = $pdo->query($sql);

//INSERT でコメント挿入
if(!empty ($_POST["com"]) && !empty($_POST["yourname"])){
	if(empty($_POST["editre"])){
	$sql = $pdo -> prepare("INSERT INTO mission (name,comment,time,pass) VALUES (:name,:comment,:time,:pass)"); //時間とパスふやしたが
	$sql -> bindParam(':name', $name, PDO::PARAM_STR);
	$sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
	$sql -> bindparam(':time', $time, PDO::PARAM_STR);	//時間を挿入するため？
	$sql -> bindparam(':pass', $pass, PDO::PARAM_STR);	//パス挿入するため？
	$name = $_POST["yourname"];
	$comment = $_POST["com"]; //フォームからの名前コメントをいれたい
	$time = date("Y年m月d日 H時i分s秒");
	$pass = $_POST["password"];
	$sql -> execute();
	}else{
	$id = $_POST["editre"]; //変更する投稿番号
	$name = $_POST["yourname"];
	$comment = $_POST["com"]; 
	$time =date("Y年m月d日 H時i分s秒");
	$pass = $_POST["password"];
	$sql = 'update mission set name=:name,comment=:comment,time=:time,pass=:pass where id=:id';
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':name', $name, PDO::PARAM_STR);
	$stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
	$stmt->bindparam(':time' , $time, PDO::PARAM_STR);
	$stmt->bindparam(':pass' , $pass, PDO::PARAM_STR);
	$stmt->bindParam(':id', $id, PDO::PARAM_INT);
	$stmt->execute();
	}
}

//delete削除
if(!empty($_POST["delete"])){
	$pass=$_POST["delepassword"];
	$id = $_POST["delete"];
	$sql = 'delete from mission where id=:id and pass=:pass' ;
	$stmt = $pdo->prepare($sql);
	$stmt->bindParam(':id', $id, PDO::PARAM_INT);
	$stmt->bindParam(':pass',$pass, PDO::PARAM_STR);
	$stmt->execute();
}

//編集
if(!empty($_POST["edit"])){
$sql = 'SELECT * FROM mission where id=:id and pass=:pass';
	$pass=$_POST["edipassword"];
	$id=$_POST["edit"];
	$stmt = $pdo->prepare($sql);
	$stmt ->bindParam(':id', $id, PDO::PARAM_INT);
	$stmt->bindParam(':pass',$pass, PDO::PARAM_STR);
	$stmt ->execute();
	$results = $stmt->fetchAll();
	if(!empty($results[0])){
		$edino=$id;
		$row=$results[0];
		$ediname=$row["name"];
		$edicomm=$row["comment"];
	}	

}


//SELECTで表示	
$sql = 'SELECT * FROM mission';
	$stmt = $pdo->query($sql);
	$results = $stmt->fetchAll();
	foreach ($results as $row){
		//$rowの中にはテーブルのカラム名が入る
		echo $row['id'].',';
		echo $row['name'].',';
		echo $row['comment'].',';
		echo $row['time'].'<br>';
	echo "<hr>";
	}
?>

<html>

<form action=""  method="post"> 
名前:<input type ="text" name="yourname"
		value="<?php if(!empty($ediname)){echo $ediname;}?>"><br>

コメント:<input type="text" name="com"  
	value="<?php if(!empty($edicomm)){echo $edicomm;}?>"><br>

パスワード:<input type="password" name="password"><br>

<input type="hidden" name="editre" 
	value="<?php if(!empty($edino)){echo $edino;}?>" >


       <input type="submit" value="送信"><br>



削除対象番号：<input type = "number"  name="delete"><br>
パスワード: <input type="password" name="delepassword"><br>	
             <input type ="submit" value="削除"><br>

編集対象番号:<input type = "number" name="edit"><br>
パスワード: <input type="password" name="edipassword"><br>
		<input type="submit" value="編集"><br>

</form>
   
</html>


