<?php
header('Content-Type: text/html; charset=UTF-8');

//*****データベースへの接続*****
$dsn='mysql:dbname=データベース名;host=localhost';
$user='ユーザー名';
$password='パスワード';
$pdo=new PDO($dsn,$user,$password);

//*****テーブルの作成*****
$sql="CREATE TABLE newtable"
."("."id INT PRIMARY KEY AUTO_INCREMENT,"
."name char(32),"
."comment TEXT,"
."date DATETIME"
.");";
$stmt=$pdo->query($sql);

//テーブルにカラムを追加
$sql="alter table newtable add password char(32)";
$result=$pdo->query($sql);

//*****テーブルの表示*****
//$sql='SHOW TABLES';
//$result=$pdo->query($sql);
//foreach($result as $row){
// echo $row[0];
// echo '<br>';
//}
//echo "<hr>";

//テーブル内容の確認
//$sql='SHOW CREATE TABLE newtable';
//$result=$pdo->query($sql);
//foreach($result as $row){
//print_r($row);
//}
//echo "<hr>";


//*****定義付け******
$name=$_POST["name"];
$comment=$_POST["comment"];
date_default_timezone_set('Asia/Tokyo');
$date=date('Y/m/d H:i:s');
$delete=$_POST["delete"];
$edit=$_POST["edit"];
$number=$_POST["number"];
$Tpassword=$_POST["Tpassword"];
$Dpassword=$_POST["Dpassword"];
$Epassword=$_POST["Epassword"];


//******ここから投稿機能******

if(!empty($_POST["name"]) && !empty($_POST["comment"]) && empty($_POST["number"]) && !empty($_POST["Tpassword"])){
//名前、コメント、パスワード全てが入力されたとき、かつ編集モードでないとき
$sql=$pdo->prepare("INSERT INTO newtable(name,comment,date,password)VALUES(:name,:comment,:date,:password)");
//テーブルのname,comment,date,passwordに:name,:comment,:date,:passwordのパラメータを与える。
$sql->bindParam(':name',$name,PDO::PARAM_STR);
$sql->bindParam(':comment',$comment,PDO::PARAM_STR);
$sql->bindParam(':date',$date,PDO::PARAM_STR);
$sql->bindParam(':password',$Tpassword,PDO::PARAM_STR);
//:nameは$nameに置き換える。他も同じ

$sql->execute();
//これがないとうまくいかないらしい。終わりの宣言？

}


//*******ここからは削除機能********
if(!empty($_POST["delete"]) && !empty($_POST["Dpassword"])){
//削除対象番号とパスワードが入力されたとき
$sql="SELECT * FROM newtable order by id asc";
//テーブルから情報を取り出す
$result=$pdo->query($sql);
//実行する

foreach($result as $row){

if($row["id"]==$delete){
if($row["password"]==$Dpassword){
$sql="delete from newtable where id=$delete";
//$daleteと同じ番号を消去する
$result=$pdo->query($sql);
}
else{
echo "パスワードが違います。";
}
}
}

}


//*****ここからは編集機能*****

//編集したい内容を入力フォームへの表示する
if(!empty($_POST["edit"]) && !empty($_POST["Epassword"])){

$sql="SELECT * FROM newtable order by id asc";
$result=$pdo->query($sql);

foreach($result as $row){
if($edit==$row['id']){
if($row['password']==$Epassword){
$edit_number=$row['id'];
$edit_name=$row['name'];
$edit_comment=$row['comment'];
}
else{
echo "パスワードが違います。";
}

}
}
}

//編集実行
if(!empty($_POST["name"]) && !empty($_POST["comment"]) && !empty($_POST["number"])){

$sql="update newtable set name='$name',comment='$comment',date='$date' where id=$number";
$result=$pdo->query($sql);

}

?>
<!DOCTYPE html>
 <html lang="ja">
  <head>
   <mata charset="UTF-8">
   <title>mission_4-1</title>
  </head>
  <body>
   <form action="mission_4-1.php" method="post">
   <input type="text" name="name" placeholder="名前" value=<?php echo $edit_name; ?>><br>
   <input type="text" name="comment" placeholder="コメント" value=<?php echo $edit_comment; ?>><br>
   <input type="text" name="Tpassword" placeholder="パスワード">
   <input type="hidden" name="number" value=<?php echo $edit_number; ?>>
   <input type="submit" value="送信"><br><br>
   <input type="text" name="delete" placeholder="削除対象番号"><br>
   <input type="text" name="Dpassword" placeholder="パスワード">
   <input type="submit" value="削除"><br><br>
   <input type="text" name="edit" placeholder="編集対象番号"><br>
   <input type="text" name="Epassword" placeholder="パスワード">
   <input type="submit" value="編集"><br><br>

<?php

//入力したデータをselectによって表示する
$sql='SELECT * FROM newtable order by id asc';
$results=$pdo->query($sql);
$result=$results->fetchALL();
foreach($result as $row){

echo $row['id'].': ';
echo $row['name'].' 『';
echo $row['comment'].'』 ';
echo $row['date'].'<br>';

}

?>
  </body>
 </html>
