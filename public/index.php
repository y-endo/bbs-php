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

  if (!empty($_POST['name']) && !empty($_POST['message'])) {
    if ($file_handle = fopen(FILENAME, 'a')) {
      // 書き込み日時を取得
      $now = date('Y-m-d H:i:s');
      // 書き込むデータを作成
      $data = "{$_POST['name']}***{$_POST['message']}***{$now}\n";
      // 書き込み
      fwrite($file_handle, $data);
      // ファイルを閉じる
      fclose($file_handle);
    }
  }

  if ($file_handle = fopen(FILENAME, 'r')) {
    while($data = fgets($file_handle)) {
      $split_data = preg_split('/\*\*\*/', $data);
      $message = array(
        'name' => $split_data[0],
        'message' => $split_data[1],
        'post_date' => $split_data[2]
      );
      array_unshift($message_array, $message);
    }

    // ファイルを閉じる
    fclose($file_handle);
  }
?>
<section class="section">
  <div class="container">
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
          <button class="button is-link">書き込む</button>
        </div>
      </div>
    </form>
    <div class="is-divider"></div>
    <?php if (!empty($message_array)): ?>
    <?php foreach($message_array as $value): ?>
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
    <?php endforeach; ?>
    <?php endif; ?>
  </div>
</section>
</body>
</html>
