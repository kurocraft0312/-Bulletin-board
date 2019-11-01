<?php

namespace MyApp;

class ImageUploader {
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
        // サムネイル作成
        } catch (\Exception $e) {
            echo $e->getMessage();
            exit;
        }
        // 画像投稿した後に再読み込みしてしまうと、画像が二重投稿されてしまう。
        header('Location: http://'.$_SERVER['HTTP_HOST']);
        exit;
    }

    private function _validateImageType() {
        $imageType = exif_imagetype($_FILES['image']['tmp_name']);
        switch($imageType) {
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

    private function _validateUpload() {
        // arrayが0になった
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