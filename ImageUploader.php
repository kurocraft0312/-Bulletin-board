<?php

namespace MyApp;

class ImageUploader {
    public function upload() {
        // 上手くいかなかった時のエラーメッセージ
        try {
        // エラーチェック
        // 画像タイプチェック
        // 画像保存
        // サムネイル作成
        } catch (\Exception $e) {
            echo $e->getMessage();
            exit;
        }
        // 画像投稿した後に再読み込みしてしまうと、画像が二重投稿されてしまう。
        
    }
}

?>