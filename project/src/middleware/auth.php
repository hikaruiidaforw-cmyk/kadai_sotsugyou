<?php
/**
 * 認証ミドルウェア
 */

class Auth {
    /**
     * セッション開始
     */
    public static function startSession(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start([
                'cookie_lifetime' => SESSION_LIFETIME,
                'cookie_secure' => isset($_SERVER['HTTPS']),
                'cookie_httponly' => true,
                'cookie_samesite' => 'Strict'
            ]);
        }
    }

    /**
     * ログイン済みかチェック
     */
    public static function check(): bool {
        self::startSession();
        return isset($_SESSION['user_id']) && isset($_SESSION['user_role']);
    }

    /**
     * ログインユーザー取得
     */
    public static function user(): ?array {
        if (!self::check()) {
            return null;
        }
        return [
            'id' => $_SESSION['user_id'],
            'email' => $_SESSION['user_email'] ?? '',
            'role' => $_SESSION['user_role'],
            'name' => $_SESSION['user_name'] ?? ''
        ];
    }

    /**
     * ユーザーID取得
     */
    public static function id(): ?int {
        return self::check() ? (int)$_SESSION['user_id'] : null;
    }

    /**
     * ロール取得
     */
    public static function role(): ?string {
        return self::check() ? $_SESSION['user_role'] : null;
    }

    /**
     * ログイン処理
     */
    public static function login(array $user): void {
        self::startSession();
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['user_name'] = $user['name'] ?? '';
        $_SESSION['logged_in_at'] = time();
    }

    /**
     * ログアウト処理
     */
    public static function logout(): void {
        self::startSession();
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }
        session_destroy();
    }

    /**
     * 認証必須ページのガード
     */
    public static function requireAuth(): void {
        if (!self::check()) {
            header('Location: ' . BASE_PATH . '/?page=login');
            exit;
        }
    }

    /**
     * ロール必須ページのガード
     */
    public static function requireRole(string $role): void {
        self::requireAuth();
        if (self::role() !== $role) {
            header('HTTP/1.1 403 Forbidden');
            exit('アクセス権限がありません');
        }
    }

    /**
     * 複数ロールのいずれかを持つかチェック
     */
    public static function hasAnyRole(array $roles): bool {
        return in_array(self::role(), $roles);
    }

    /**
     * ゲスト専用ページのガード（ログイン済みならリダイレクト）
     */
    public static function requireGuest(): void {
        if (self::check()) {
            $role = self::role();
            $redirect = match($role) {
                ROLE_DOCTOR => BASE_PATH . '/?page=doctor/dashboard',
                ROLE_CLINIC => BASE_PATH . '/?page=clinic/dashboard',
                ROLE_ADMIN => BASE_PATH . '/?page=admin/dashboard',
                default => BASE_PATH . '/'
            };
            header('Location: ' . $redirect);
            exit;
        }
    }
}
