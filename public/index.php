<?php
  require_once './common.php';
  session_start();

  // タイムゾーンの設定
  date_default_timezone_set('Asia/Tokyo');

  const DB_HOST = 'bbs-php-mysql';
  const DB_USER = 'bbs-php';
  const DB_PASS = 'bbs-php';
  const DB_NAME = 'bbs-php';

  $message_array = array();
  $error_message = array();

  if (!empty($_POST['submit'])) {
      $name = $_POST['name'];
      if (empty($name)) {
          $error_message[] = '名前を入力してください。';
      } else {
          $name = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');

          // セッションに名前を保存
          $_SESSION['name'] = $name;
      }

      $message = $_POST['message'];
      if (empty($message)) {
          $error_message[] = 'メッセージを入力してください。';
      } else {
          $message = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');
      }

      // PDOでmysqlに接続
      try {
          $pdo = new PDO("mysql:host={$identity(DB_HOST)};dbname={$identity(DB_NAME)};charset=utf8;", DB_USER, DB_PASS);
      } catch (PDOException $error) {
          $error_message[] = $error->getMessage();
      }

      // MySQLにデータの書き込み
      if (empty($error_message)) {
          $stmt = $pdo->prepare('INSERT INTO message (name, message, post_date) VALUES (:name, :message, :post_date)');
          $stmt->bindValue(':name', $name, PDO::PARAM_STR);
          $stmt->bindValue(':message', $message, PDO::PARAM_STR);
          $stmt->bindValue(':post_date', date('Y-m-d H:i:s'), PDO::PARAM_STR);

          if ($stmt->execute()) {
              $_SESSION['success_message'] = 'メッセージを書き込みました。';

              header('Location: ./');
          } else {
              $error_message = $stmt->errorInfo();
          }
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
      $message_array = $pdo->query('SELECT name, message, post_date FROM message ORDER BY post_date DESC')->fetchAll(PDO::FETCH_ASSOC);
  }
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.8.0/css/bulma.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma-extensions@6.2.7/bulma-divider/dist/css/bulma-divider.min.css">
<title>掲示板</title>
</head>
<body>
<section class="section">
  <div class="container">
    <h1 class="title">掲示板</h1>
    <?php if (empty($_POST['submit']) && !empty($_SESSION['success_message'])): ?>
    <div class="notification is-success">
      <button class="delete"></button>
      <?= $_SESSION['success_message']; ?>
      <?php unset($_SESSION['success_message']); ?>
    </div>
    <?php endif; ?>
    <?php if (!empty($error_message)): ?>
    <div class="notification is-danger">
      <button class="delete"></button>
      <?php foreach ($error_message as $value): ?>
      <?= $value ?><br>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
    <form method="post">
      <div class="field">
        <label class="label">名前</label>
        <div class="control">
          <input class="input" type="text" placeholder="名前" name="name" value="<?php if (!empty($_SESSION['name'])) {
    echo $_SESSION['name'];
} ?>">
        </div>
      </div>
      <div class="field">
        <label class="label">メッセージ</label>
        <div class="control">
          <textarea class="textarea" placeholder="メッセージ" name="message"></textarea>
        </div>
      </div>
      <div class="field">
        <div class="control">
          <button class="button is-link" name="submit" value="write">書き込む</button>
        </div>
      </div>
    </form>
    <div class="is-divider"></div>
    <?php if (!empty($message_array)): ?>
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
        </div>
      </article>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>
  </div>
</section>
</body>
</html>
