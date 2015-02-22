<?php

/* Copyright © 2014 Hiroki's Co.Ltd. All Rights Reserved. */

/**
 * loginPageの表示.
 *
 * @author 5/18/2014 Hiroki Nogami
 */

  session_start();
  
  // エラーメッセージ
  $errorMessage = "";
  // 画面に表示するため特殊文字をエスケープする
  $viewUserId = htmlspecialchars($_POST["userid"], ENT_QUOTES);

  // ログインボタンが押された場合      
  if (isset($_POST["login"])) {

/**
 * 認証用ID,Passwordの管理.
 *
 * @author 5/18/2014 Hiroki Nogami
 */  	
  	
    // 認証成功
    if ($_POST["userid"] == "hiroki" && $_POST["password"] == "nogami" ||
		$_POST["userid"] == "tester" && $_POST["password"] == "tester") {
      
      // セッションIDを新規に発行する
      session_regenerate_id(TRUE);
      $_SESSION["USERID"] = $_POST["userid"];
      //header("Location:PlayerPage.php");
      header("Location:SelectMusic.php");
      exit;
    }
    else {
      $errorMessage = "ユーザIDあるいはパスワードに誤りがあります。";
    }
  }

?>

<!doctype html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>login</title>
  </head>
  <body>
  <form id="loginForm" name="loginForm" action="<?php print($_SERVER['PHP_SELF']) ?>" method="POST">
  <fieldset>
  <legend>LoginForm</legend>
  <div><?php echo $errorMessage ?></div>
  <label for="userid">UserId</label><input type="text" id="userid" name="userid" placeholder="firstName" value="<?php echo $viewUserId ?>">
  <br>
  <label for="password">Password</label><input type="password" id="password" name="password" placeholder="lastName" value="">
  <br>
  <label></label><input type="submit" id="login" name="login" value="login">
  </fieldset>
  </form>
  </body>
</html>