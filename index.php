<?php 
// ini_setはエラーをブラウザ上に表示する処理のこと
ini_set('display_errors',1);
define('MAX_FILE_SIZE',1 * 1024 * 1024); /*1MB=1024KB 1は1KB換算 */
define('THUMBNAIL_WIDTH',400);
define('IMAGES_DIR',__DIR__ .'/assets/img');
define('THUMBNAIL',__DIR__ .'/assets/thumb');

if(!function_exists('imagecreatetruecolor')) {
    echo 'GD not installed';/*画像の処理に使うGDというプラグインがあるかどうかのチェック*/
    exit;
}

function h($s) {
    return htmlspecialchars($s,ENT_QUOTES,'UTF-8');
}

require 'ImageUploader.php';

$uploader = new \MyApp\ImageUploader();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $uploader->upload();
}

?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="assets/css/normalize.css">
    <link rel="stylesheet" href="style.css">
    <title>Bulletin-board</title>
</head>
<body>
    <form action="" method="post">
        <!-- 隠しコマンドでファイルのアップロードサイズを定義 -->
        <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo h(MAX_FILE_SIZE); ?>">
        <input type="file" name="image">
        <input type="submit" value="upload">
    </form>
</body>
</html>