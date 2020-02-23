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
<?php
  // メッセージを保存するファイルのパス
  const FILENAME = './message.txt';

  // タイムゾーンの設定
  date_default_timezone_set('Asia/Tokyo');

  $now = null;
  $data = null;
  $file_handle = null;
  $message_array = array();
  $success_message = null;
  $error_message = array();

  if (!empty($_POST['submit'])) {
    $name = $_POST['name'];
    if (empty($name)) {
      $error_message[] = '名前を入力してください。';
    } else {
      $name = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
      $name = preg_replace('/\\r\\n|\\n|\\r/', '', $name);
    }

    $message = $_POST['message'];
    if (empty($message)) {
      $error_message[] = 'メッセージを入力してください。';
    } else {
      $message = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');
      $message = preg_replace('/\\r\\n|\\n|\\r/', '<br>', $message);
    }

    // PDOでmysqlに接続
    try {
      $pdo = new PDO('mysql:host=bbs-php-mysql;dbname=bbs-php;charset=utf8;', 'bbs-php', 'bbs-php');
    } catch (PDOException $error) {
      $error_message[] = $error->getMessage();
    }

    // MySQLにデータの書き込み
    if (empty($error_message)) {
      $stmt = $pdo->prepare('INSERT INTO message (name, message, post_date) VALUES (:name, :message, :post_date)');
      $stmt->bindValue(':name', $name);
      $stmt->bindValue(':message', $message);
      $stmt->bindValue(':post_date', date('Y-m-d H:i:s'));

      if ($stmt->execute()) {
        $success_message = 'メッセージを書き込みました。';
      } else {
        $error_message = $stmt->errorInfo();
      }
    }
  }

  // PDOでmysqlに接続
  try {
    $pdo = new PDO('mysql:host=bbs-php-mysql;dbname=bbs-php;charset=utf8;', 'bbs-php', 'bbs-php');
  } catch (PDOException $error) {
    $error_message[] = $error->getMessage();
  }

  // MySQLからデータを取得
  if (empty($error_message)) {
    $message_array = $pdo->query('SELECT name, message, post_date FROM message ORDER BY post_date DESC')->fetchAll(PDO::FETCH_ASSOC);
  }
?>
<section class="section">
  <div class="container">
    <h1 class="title">掲示板</h1>
    <?php if (!empty($success_message)): ?>
    <div class="notification is-success">
      <button class="delete"></button>
      <?= $success_message ?>
    </div>
    <?php endif; ?>
    <?php if (!empty($error_message)): ?>
    <div class="notification is-danger">
      <button class="delete"></button>
      <?php foreach($error_message as $value): ?>
      <?= $value ?><br>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
    <form method="post">
      <div class="field">
        <label class="label">名前</label>
        <div class="control">
          <input class="input" type="text" placeholder="名前" name="name">
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
    <?php foreach($message_array as $value): ?>
    <div class="box">
      <article class="media">
        <div class="media-content">
          <div class="content">
            <p>
              <strong><?= $value['name'] ?></strong> <small><?= date('Y年m月d日 H:i', strtotime($value['post_date'])) ?></small>
              <br>
              <?= $value['message'] ?>
            </p>
          </div>
        </div>
        <div class="media-right">
          <button class="delete"></button>
        </div>
      </article>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>
  </div>
</section>
</body>
</html>
