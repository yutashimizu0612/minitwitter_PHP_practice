<?php
require('dbconnect.php');
session_start();

if(isset($_SESSION['id']) && $_SESSION['time'] + 3600 > time()){
  //ログインしている
  $_SESSION['time'] = time();
  //ログイン中のメンバー情報を取得
  $members = $db->prepare('SELECT * FROM members WHERE id=?');
  $members->execute(array($_SESSION['id']));
  $member = $members->fetch();
} else {
  header('Location:index.php');
  exit();
}

//ダイレクトメッセージを記録する
if(!empty($_POST)){
  if(isset($_POST['directMessage'])){
    $dm = $db->prepare('INSERT INTO directMessages SET dm_message=?,member_id=?,created=NOW()');
    $dm->execute(array(
      $_POST['directMessage'],
      $member['id'],
    ));
    header('Location:index.php');
    exit();
  }
}

  //DM先の投稿情報を取得
  $directMessages = $db->prepare('SELECT m.name,m.picture,p.* FROM members m,posts p WHERE m.id=p.member_id AND p.id=? ORDER BY p.created DESC');
  $directMessages->execute(array($_REQUEST['dm']));

//htmlspecialcharsのショートカット
function h($value){
  return htmlspecialchars($value,ENT_QUOTES);
}

//本文内のURLにリンクを設定する
function makeLink($value){
  return mb_ereg_replace("(https?)(://[[:alnum:]\+\$\;\?\.%,!#~*/:@&=_-]+)",'<a href="\1\2">\1\2</a>', $value);
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>ひとこと掲示板</title>

  <link rel="stylesheet" href="./style.css" />
</head>

<body>
<div id="wrap">
  <div id="head">
    <h1>ダイレクトメッセージ</h1>
  </div>
  <div id="content">
    <form action="" method="post">
      <dl>
        <dt><?php echo h($member['name']); ?>さん、〜さんへメッセージをどうぞ</dt>
        <dd>
<textarea name="directMessage" cols="50" rows="5">
</textarea>
        </dd>
      </dl>
      <div>
        <input type="submit" value="投稿する">
      </div>
    </form>
    <?php foreach($directMessages as $directMessage): ?>
    <div class="msg">
      <img src="member_picture/<?php echo h($directMessage['picture']); ?>" width="48" height="48"; />
      <p><?php echo makeLink(h($directMessage['message']));?><span class="name">（<?php echo h($directMessage['name']); ?>）</span>
      [<a href="index.php?res=<?php echo h($directMessage['id']); ?>">Re</a>]</p>
      [<a href="index.php?rt=<?php echo h($directMessage['id']); ?>">RT</a>]</p>
      [<a href="directMessage.php?dm=<?php echo h($directMessage['id']); ?>">DM</a>]</p>
      <p class="day">
        <a href="view.php?id=<?php echo h($directMessage['id']); ?>">
         <?php echo h($directMessage['created']); ?>
        </a>
        <?php if($directMessage['reply_post_id'] > 0): ?>
        <a href="view.php?id=<?php echo h($directMessage['reply_post_id']); ?>">返信先のメッセージ</a>
        <?php endif; ?>
        <?php if($_SESSION['id'] == $directMessage['member_id']): ?>
          [<a href="delete.php?id=<?php echo h($directMessage['id']); ?>" style="color:#F33">削除</a>]
        <?php endif; ?>
      </p>
    </div>
<?php endforeach; ?>
  </div>

</div>
</body>
</html>
