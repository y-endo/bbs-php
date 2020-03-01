<?php
  require_once '../common.php';

  session_start();

  // タイムゾーンの設定
  date_default_timezone_set('Asia/Tokyo');

  const DB_HOST = 'bbs-php-mysql';
  const DB_USER = 'bbs-php';
  const DB_PASS = 'bbs-php';
  const DB_NAME = 'bbs-php';

  const ADMIN_PASS = 'password';

  $message_array = array();
  $error_message = array();

  // ログアウト（セッション削除）
  if (!empty($_GET['logout'])) {
      unset($_SESSION['admin_login']);
  }

  if (!empty($_POST['submit'])) {
      if (!empty($_POST['pass']) && $_POST['pass'] === ADMIN_PASS) {
          $_SESSION['admin_login'] = true;
      } else {
          $error_message[] = 'ログインに失敗しました。';
      }
  }

  // PDOでmysqlに接続
  try {
      $pdo = new PDO("mysql:host={$identity(DB_HOST)};dbname={$identity(DB_NAME)};charset=utf8;", DB_USER, DB_PASS);
  } catch (PDOException $error) {
      $error_message[] = $error->getMessage();
  }

  // MySQLからデータを取得
  if (empty($error_message)) {
      $message_array = $pdo->query('SELECT * FROM message ORDER BY post_date DESC')->fetchAll(PDO::FETCH_ASSOC);
  }
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.8.0/css/bulma.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma-extensions@6.2.7/bulma-divider/dist/css/bulma-divider.min.css">
<title>管理ページ | 掲示板</title>
</head>
<body>
<section class="section">
  <div class="container">
    <h1 class="title">管理ページ | 掲示板</h1>
    <?php if (!empty($error_message)): ?>
    <div class="notification is-danger">
      <button class="delete"></button>
      <?php foreach ($error_message as $value): ?>
      <?= $value ?><br>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
    <?php if (!empty($_SESSION['admin_login']) && $_SESSION['admin_login'] === true): ?>
    <?php if (!empty($message_array)): ?>
    <div class="content">
      <form method="get" action="./download.php">
        <nav class="level is-mobile">
          <div class="level-left">
            <div class="level-item">
              <div class="field">
                <div class="control">
                  <div class="select">
                    <select name="limit">
                      <option value="" selected>全て</option>
                      <option value="10">10件</option>
                      <option value="30">30件</option>
                      <option value="50">50件</option>
                    </select>
                  </div>
                </div>
              </div>
            </div>
            <div class="level-item">
              <div class="field">
                <div class="control">
                  <button class="button is-link" name="download" value="true">ダウンロード</button>
                </div>
              </div>
            </div>
          </div>
        </nav>
      </form>
    </div>
    <div class="content">
      <form method="get">
        <div class="field">
          <div class="control">
            <button class="button is-dark" name="logout" value="logout">ログアウト</button>
          </div>
        </div>
      </form>
    </div>
    <?php foreach ($message_array as $value): ?>
    <div class="box">
      <article class="media">
        <div class="media-content">
          <div class="content">
            <p>
              <strong><?= $value['name'] ?></strong> <small><?= date('Y年m月d日 H:i', strtotime($value['post_date'])) ?></small>
              <br>
              <?= nl2br($value['message']) ?>
            </p>
          </div>
          <nav class="level is-mobile">
            <div class="level-left">
              <a href="./edit.php?id=<?= $value['id'] ?>" class="level-item">編集</a>
              <a href="./delete.php?id=<?= $value['id'] ?>" class="level-item">削除</a>
            </div>
          </nav>
        </div>
      </article>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>
    <?php else: ?>
    <form method="post">
      <div class="field">
        <label class="label">パスワード</label>
        <div class="control">
          <input class="input" type="password" name="pass">
        </div>
      </div>
      <div class="field">
        <div class="control">
          <button class="button is-link" name="submit" value="login">ログイン</button>
        </div>
      </div>
    </form>
    <?php endif; ?>
  </div>
</section>
</body>
</html>
