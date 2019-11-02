<?php

namespace MyApp;

class ImageUploader {

    private $_imageFileName;
    private $_imageType;

    public function upload() {
        // 上手くいかなかった時のエラーメッセージ
        try {
        // エラーチェック
        $this->_validateUpload();

        // 画像タイプチェック
        $ext = $this->_validateImageType();
        // var_dump($ext);
        // exit;

        // 画像保存
        $savePath = $this->_save($ext);
        // サムネイル作成
        $this->_createThumbnail($savePath);

        } catch (\Exception $e) {
            echo $e->getMessage();
            exit;
        }
        // 画像投稿した後に再読み込みしてしまうと、画像が二重投稿されてしまう。
        header('Location: http://'.$_SERVER['HTTP_HOST']);
        exit;
    }

    public function getImages() {
        $images = [];
        $files = [];
        $imageDir = opendir(IMAGES_DIR);
        while (false !== ($file = readdir($imageDir))) {
            if ($file === '.' || $file === '..') {
                continue;
            }
            $files[] = $file;
            if (file_exists(THUMBNAIL_DIR . '/' . $file)) {
                $images[] = '/assets' . '/'. basename(THUMBNAIL_DIR) . '/' . $file;
            } else {
                $images[] = '/assets'. '/' . basename(IMAGES_DIR) . '/' . $file;
            }
        }
        array_multisort($files, SORT_DESC, $images);
        return $images;
    }

    // サムネイル作成
    private function _createThumbnail($savePath) {
        $imageSize = getimagesize($savePath);
        $width = $imageSize[0];
        $height = $imageSize[1];
        if ($width > THUMBNAIL_WIDTH) {
            $this->_createThumbnailMain($savePath,$width,$height);
        }
    }

    // サムネイル作成の詳細
    private function _createThumbnailMain($savePath,$width,$height) {
        switch($this->_imageType) {
            case IMAGETYPE_GIF:
                $srcImage = imagecreatefromgif($savePath);
                break;
            case IMAGETYPE_JPEG:
                $srcImage = imagecreatefromjpeg($savePath);
                break;
            case IMAGETYPE_PNG:
                $srcImage = imagecreatefrompng($savePath);
                break;
        }
        $thumbHeight = round($height * THUMBNAIL_WIDTH / $width);
        $thumbImage = imagecreatetruecolor(THUMBNAIL_WIDTH,$thumbHeight);
        imagecopyresampled($thumbImage,$srcImage,0,0,0,0,THUMBNAIL_WIDTH,$thumbHeight,$width,$height);

        switch($this->_imageType) {
            case IMAGETYPE_GIF:
                imagegif($thumbImage, THUMBNAIL_DIR . '/' . $this->_imageFileName);
                break;
            case IMAGETYPE_JPEG:
                imagejpeg($thumbImage, THUMBNAIL_DIR . '/' . $this->_imageFileName);
                break;
            case IMAGETYPE_PNG:
                imagepng($thumbImage, THUMBNAIL_DIR . '/' . $this->_imageFileName);
                break;
        }

    }

    // 画像の保存を観測する
    private function _save($ext) {
        $this->_imageFileName = sprintf(
            '%s_%s.%s',
            time(),
            sha1(uniqid(mt_rand(),true)),
            $ext
        );
        $savePath = IMAGES_DIR . '/' . $this->_imageFileName;
        $res = move_uploaded_file($_FILES['image']['tmp_name'],$savePath);
        if($res === false) {
            throw new \Exception('Could not upload!'); 
        }
        return $savePath;
    }

    // バリデーションで画像の拡張子エラーを観測する
    private function _validateImageType() {
        $this->_imageType = exif_imagetype($_FILES['image']['tmp_name']);
        switch($this->_imageType) {
            case IMAGETYPE_GIF:
                return 'gif';
            case IMAGETYPE_JPEG:
                return 'jpg';
            case IMAGETYPE_PNG:
                return 'png';
            default:
                throw new \Exception('PNG/JPEG/GIF only!');
        }
    }

    // バリデーションで画像アップロードのエラーを観測する
    private function _validateUpload() {
        // var_dump($_FILES);
        // exit;

        if(!isset($_FILES['image']) || !isset($_FILES['image']['error'])) {
            throw new \Exception('Upload Error!');
        }

        switch($_FILES['image']['error']) {
            case UPLOAD_ERR_OK:
                return true;
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                // ファイルが指定サイズより大きければ
                throw new \Exception('File too large!');
            default:
                // 何らかのエラーなら
                throw new \Exception('Err: '.$_FILES['image']['error']);
        }
    }
}

?>