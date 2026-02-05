<?php
/**
 * バリデーションヘルパー
 */

class Validator {
    private array $errors = [];
    private array $data = [];

    public function __construct(array $data) {
        $this->data = $data;
    }

    /**
     * 必須チェック
     */
    public function required(string $field, string $label): self {
        if (!isset($this->data[$field]) || trim($this->data[$field]) === '') {
            $this->errors[$field][] = "{$label}は必須です";
        }
        return $this;
    }

    /**
     * メールアドレス形式チェック
     */
    public function email(string $field, string $label): self {
        if (isset($this->data[$field]) && !empty($this->data[$field])) {
            if (!filter_var($this->data[$field], FILTER_VALIDATE_EMAIL)) {
                $this->errors[$field][] = "{$label}の形式が正しくありません";
            }
        }
        return $this;
    }

    /**
     * パスワード形式チェック（8文字以上、英数字混合）
     */
    public function password(string $field, string $label): self {
        if (isset($this->data[$field]) && !empty($this->data[$field])) {
            if (!preg_match('/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d@$!%*#?&]{8,}$/', $this->data[$field])) {
                $this->errors[$field][] = "{$label}は8文字以上で、英字と数字を含む必要があります";
            }
        }
        return $this;
    }

    /**
     * パスワード確認チェック
     */
    public function passwordConfirm(string $field, string $confirmField, string $label): self {
        if (isset($this->data[$field]) && isset($this->data[$confirmField])) {
            if ($this->data[$field] !== $this->data[$confirmField]) {
                $this->errors[$confirmField][] = "{$label}が一致しません";
            }
        }
        return $this;
    }

    /**
     * 最小文字数チェック
     */
    public function minLength(string $field, int $min, string $label): self {
        if (isset($this->data[$field]) && mb_strlen($this->data[$field]) < $min) {
            $this->errors[$field][] = "{$label}は{$min}文字以上で入力してください";
        }
        return $this;
    }

    /**
     * 最大文字数チェック
     */
    public function maxLength(string $field, int $max, string $label): self {
        if (isset($this->data[$field]) && mb_strlen($this->data[$field]) > $max) {
            $this->errors[$field][] = "{$label}は{$max}文字以内で入力してください";
        }
        return $this;
    }

    /**
     * 電話番号形式チェック
     */
    public function phone(string $field, string $label): self {
        if (isset($this->data[$field]) && !empty($this->data[$field])) {
            $phone = preg_replace('/[-\s]/', '', $this->data[$field]);
            if (!preg_match('/^0\d{9,10}$/', $phone)) {
                $this->errors[$field][] = "{$label}の形式が正しくありません";
            }
        }
        return $this;
    }

    /**
     * 郵便番号形式チェック
     */
    public function postalCode(string $field, string $label): self {
        if (isset($this->data[$field]) && !empty($this->data[$field])) {
            $postal = preg_replace('/[-\s]/', '', $this->data[$field]);
            if (!preg_match('/^\d{7}$/', $postal)) {
                $this->errors[$field][] = "{$label}の形式が正しくありません";
            }
        }
        return $this;
    }

    /**
     * 数値チェック
     */
    public function numeric(string $field, string $label): self {
        if (isset($this->data[$field]) && !empty($this->data[$field])) {
            if (!is_numeric($this->data[$field])) {
                $this->errors[$field][] = "{$label}は数値で入力してください";
            }
        }
        return $this;
    }

    /**
     * 日付形式チェック
     */
    public function date(string $field, string $label): self {
        if (isset($this->data[$field]) && !empty($this->data[$field])) {
            $date = date_parse($this->data[$field]);
            if ($date['error_count'] > 0 || !checkdate($date['month'], $date['day'], $date['year'])) {
                $this->errors[$field][] = "{$label}の形式が正しくありません";
            }
        }
        return $this;
    }

    /**
     * エラーがあるかチェック
     */
    public function fails(): bool {
        return !empty($this->errors);
    }

    /**
     * エラー取得
     */
    public function getErrors(): array {
        return $this->errors;
    }

    /**
     * サニタイズ
     */
    public static function sanitize($value): string {
        if (is_array($value)) {
            return array_map([self::class, 'sanitize'], $value);
        }
        return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
    }

    /**
     * HTMLをエスケープして出力
     */
    public static function e($value): string {
        return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
    }
}

/**
 * エスケープ関数のショートカット
 */
function e($value): string {
    return Validator::e($value);
}
