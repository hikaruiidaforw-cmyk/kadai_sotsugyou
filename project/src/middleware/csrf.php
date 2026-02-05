<?php
/**
 * CSRF対策ミドルウェア
 */

class CSRF {
    private const TOKEN_NAME = 'csrf_token';

    /**
     * トークン生成
     */
    public static function generateToken(): string {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION[self::TOKEN_NAME])) {
            $_SESSION[self::TOKEN_NAME] = bin2hex(random_bytes(32));
        }

        return $_SESSION[self::TOKEN_NAME];
    }

    /**
     * トークン取得
     */
    public static function getToken(): string {
        return self::generateToken();
    }

    /**
     * hidden input生成
     */
    public static function tokenField(): string {
        $token = self::generateToken();
        return '<input type="hidden" name="' . self::TOKEN_NAME . '" value="' . $token . '">';
    }

    /**
     * トークン検証
     */
    public static function verify(?string $token = null): bool {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $token = $token ?? ($_POST[self::TOKEN_NAME] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');

        if (empty($token) || !isset($_SESSION[self::TOKEN_NAME])) {
            return false;
        }

        return hash_equals($_SESSION[self::TOKEN_NAME], $token);
    }

    /**
     * CSRF検証ガード（失敗時は403）
     */
    public static function requireValid(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !self::verify()) {
            header('HTTP/1.1 403 Forbidden');
            exit('不正なリクエストです');
        }
    }

    /**
     * トークンリフレッシュ
     */
    public static function refresh(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        unset($_SESSION[self::TOKEN_NAME]);
        self::generateToken();
    }
}
