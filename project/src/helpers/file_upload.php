<?php
/**
 * ファイルアップロードヘルパー
 */

class FileUpload {
    private array $errors = [];

    /**
     * 画像アップロード
     */
    public function uploadImage(array $file, string $subDir = ''): ?string {
        return $this->upload($file, $subDir, ALLOWED_IMAGE_TYPES, MAX_IMAGE_SIZE);
    }

    /**
     * ドキュメントアップロード
     */
    public function uploadDocument(array $file, string $subDir = ''): ?string {
        return $this->upload($file, $subDir, ALLOWED_DOC_TYPES, MAX_FILE_SIZE);
    }

    /**
     * ファイルアップロード共通処理
     */
    private function upload(array $file, string $subDir, array $allowedTypes, int $maxSize): ?string {
        // エラーチェック
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $this->errors[] = 'ファイルのアップロードに失敗しました';
            return null;
        }

        // ファイルサイズチェック
        if ($file['size'] > $maxSize) {
            $this->errors[] = 'ファイルサイズが大きすぎます（最大: ' . ($maxSize / 1024 / 1024) . 'MB）';
            return null;
        }

        // MIMEタイプチェック
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);
        if (!in_array($mimeType, $allowedTypes)) {
            $this->errors[] = '許可されていないファイル形式です';
            return null;
        }

        // 保存先ディレクトリ作成
        $uploadDir = UPLOAD_PATH . ($subDir ? '/' . $subDir : '');
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // ファイル名生成（ランダム文字列）
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = bin2hex(random_bytes(16)) . '.' . strtolower($extension);
        $filepath = $uploadDir . '/' . $filename;

        // ファイル移動
        if (!move_uploaded_file($file['tmp_name'], $filepath)) {
            $this->errors[] = 'ファイルの保存に失敗しました';
            return null;
        }

        // 相対パスを返す
        return ($subDir ? $subDir . '/' : '') . $filename;
    }

    /**
     * ファイル削除
     */
    public function delete(string $relativePath): bool {
        $filepath = UPLOAD_PATH . '/' . $relativePath;
        if (file_exists($filepath)) {
            return unlink($filepath);
        }
        return false;
    }

    /**
     * エラー取得
     */
    public function getErrors(): array {
        return $this->errors;
    }

    /**
     * エラーがあるかチェック
     */
    public function hasErrors(): bool {
        return !empty($this->errors);
    }
}
