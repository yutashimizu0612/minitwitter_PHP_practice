<?php
require('dbconnect.php');
session_start();

if(isset($_SESSION['id'])){
  $id = $_REQUEST['id'];

  //投稿を検査する
  $messages = $db->prepare('SELECT * FROM posts WHERE id=?');
  $messages->execute(array($id));
  $message = $messages->fetch();

  if($message['member_id'] == $_SESSION['id']){
    //削除する
    $delete = $db->prepare('DELETE FROM posts WHERE id=?');
    $delete->execute(array($id));
  }
}

header('Location:index.php');
exit();

?>
