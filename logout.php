<?php
session_start();

$_SESSION = array();
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 最終的に、セッションを破壊する
session_destroy();

//cookie情報も削除
// 有効期限を一時間前に設定します
setcookie("email", "", time() - 3600);
setcookie("password", "", time() - 3600);

header('Location:login.php');
exit();

?>
