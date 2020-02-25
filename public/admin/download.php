<?php

const DB_HOST = 'bbs-php-mysql';
const DB_USER = 'bbs-php';
const DB_PASS = 'bbs-php';
const DB_NAME = 'bbs-php';

$message_array = array();
$error_message = array();
$csv_data = null;
$limit = null;

session_start();

// 取得件数
if (!empty($_GET['limit'])) {
  $limit = (int)($_GET['limit']);
}

if (!empty($_SESSION['admin_login']) && $_SESSION['admin_login'] === true) {
  // CSV出力の設定
  header('Content-Type: application/octet-stream');
  header('Content-Disposition: attachment; filename=message.csv');
  header('Content-Transfer-Encoding: binary');

  // PDOでmysqlに接続
  try {
    $i = function ($v) { return $v; };
    $pdo = new PDO("mysql:host={$i(DB_HOST)};dbname={$i(DB_NAME)};charset=utf8;", DB_USER, DB_PASS);
  } catch (PDOException $error) {
    $error_message[] = $error->getMessage();
  }

  // MySQLからデータを取得
  if (empty($error_message)) {
    if (empty($limit)) {
      $sql = 'SELECT * FROM message ORDER BY post_date ASC';
    } else {
      $sql = "SELECT * FROM message ORDER BY post_date ASC LIMIT $limit";
    }

    $message_array = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
  }

  // CSVデータの作成
  if (!empty($message_array)) {
    // 1行目のラベル作成
    $csv_data .= '"ID", "表示名", "メッセージ", "投稿日時"'."\n";



    foreach($message_array as $value) {
      // データを1行ずつCSVファイルに書き込む
      $csv_data .= '"'.$value['id'].'","'.$value['name'].'","'.$value['message'].'","'.$value['post_date']."\"\n";
    }
  }

  // ファイルを出力
  echo $csv_data;

} else {
  // ログインページにリダイレクト
  header('Location: ./index.php');
}

return;
