<?php
require_once '../common.php';

session_start();

const DB_HOST = 'bbs-php-mysql';
const DB_USER = 'bbs-php';
const DB_PASS = 'bbs-php';
const DB_NAME = 'bbs-php';

$message_data = array();
$error_message = array();

// 管理者か
if (empty($_SESSION['admin_login']) || $_SESSION['admin_login'] !== true) {
    header('Location: ./index.php');
}

if (!empty($_GET['id']) && empty($_POST['id'])) {
    $id = (int)htmlspecialchars($_GET['id'], ENT_QUOTES);

    // PDOでmysqlに接続
    try {
        $pdo = new PDO("mysql:host={$identity(DB_HOST)};dbname={$identity(DB_NAME)};charset=utf8;", DB_USER, DB_PASS);
    } catch (PDOException $error) {
        $error_message[] = $error->getMessage();
    }

    // MySQLからデータを取得
    if (empty($error_message)) {
        $message_data = $pdo->query("SELECT * FROM message WHERE id = $id")->fetch(PDO::FETCH_ASSOC);
    }
} elseif (!empty($_POST['id'])) {
    $id = (int)htmlspecialchars($_GET['id'], ENT_QUOTES);

    // PDOでmysqlに接続
    try {
        $pdo = new PDO("mysql:host={$identity(DB_HOST)};dbname={$identity(DB_NAME)};charset=utf8;", DB_USER, DB_PASS);
    } catch (PDOException $error) {
        $error_message[] = $error->getMessage();
    }

    // MySQLからデータを削除
    if (empty($error_message)) {
        $stmt = $pdo->prepare('DELETE FROM message WHERE id = :id');
        $stmt->bindValue(':id', $id);

        if ($stmt->execute()) {
            // 削除したら一覧に戻る
            header('Location: ./index.php');
        } else {
            $error_message = $stmt->errorInfo();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.8.0/css/bulma.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma-extensions@6.2.7/bulma-divider/dist/css/bulma-divider.min.css">
<title>管理ページ（削除） | 掲示板</title>
</head>
<body>
<section class="section">
  <div class="container">
    <h1 class="title">管理ページ（削除） | 掲示板</h1>
    <?php if (!empty($error_message)): ?>
    <div class="notification is-danger">
      <button class="delete"></button>
      <?php foreach ($error_message as $value): ?>
      <?= $value ?><br>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
    <form method="post">
      <input type="hidden" name="id" value=<?= $message_data['id']; ?>>
      <div class="field">
        <label class="label">名前</label>
        <div class="control">
          <input class="input" type="text" placeholder="名前" name="name" value="<?php if (!empty($message_data['name'])) {
    echo $message_data['name'];
} ?>" disabled>
        </div>
      </div>
      <div class="field">
        <label class="label">メッセージ</label>
        <div class="control">
          <textarea class="textarea" placeholder="メッセージ" name="message" disabled><?php if (!empty($message_data['message'])) {
    echo $message_data['message'];
} ?></textarea>
        </div>
      </div>
      <div class="field">
        <div class="control">
          <a href="./index.php" class="button">一覧に戻る</a>
          <button class="button is-link" name="submit" value="delete">削除</button>
        </div>
      </div>
    </form>
  </div>
</section>
</body>
</html>
