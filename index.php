<?php 

session_start();
// ini_setはエラーをブラウザ上に表示する処理のこと
ini_set('display_errors',1);
define('MAX_FILE_SIZE',1 * 1024 * 1024); /*1MB=1024KB 1は1KB換算 */
define('THUMBNAIL_WIDTH',400);
define('IMAGES_DIR',__DIR__ .'/assets/images');
define('THUMBNAIL_DIR',__DIR__ .'/assets/thumbs');

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

list($success, $error) = $uploader->getResults();

$images = $uploader->getImages();

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

<div class="btn">
    Upload!
    <form action="" method="post" enctype="multipart/form-data" id="my_form">
        <!-- 隠しコマンドでファイルのアップロードサイズを定義 -->
        <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo h(MAX_FILE_SIZE); ?>">
        <input type="file" name="image" id="my_file">
    </form>
</div>

    <?php if(isset($success)) : ?>
        <div class="msg success"><?php echo h($success); ?></div>
    <?php endif; ?>
    <?php if(isset($error)) : ?>
        <div class="msg error"><?php echo h($error); ?></div>
    <?php endif; ?>

    <ul>
        <?php foreach ($images as $image) : ?>
        <li>
            <a href="<?php echo '/assets' . '/' . h(basename(IMAGES_DIR)) . '/' . basename($image); ?>">
                <img src="<?php echo '/assets' . '/' . h(basename($image)); ?>">
            </a>
        </li>
        <?php endforeach; ?>
    </ul>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="assets/js/index.js"></script>
</body>
</html>