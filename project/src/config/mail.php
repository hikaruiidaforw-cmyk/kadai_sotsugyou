<?php
/**
 * メール設定
 */

define('MAIL_FROM', 'noreply@medcareer-bridge.jp');
define('MAIL_FROM_NAME', 'MedCareer Bridge');
define('MAIL_REPLY_TO', 'support@medcareer-bridge.jp');

// SMTP設定（本番環境用）
define('SMTP_HOST', 'smtp.example.com');
define('SMTP_PORT', 587);
define('SMTP_USER', '');
define('SMTP_PASS', '');
define('SMTP_SECURE', 'tls');

/**
 * メール送信関数
 */
function sendMail(string $to, string $subject, string $body, array $options = []): bool {
    $headers = [
        'From' => MAIL_FROM_NAME . ' <' . MAIL_FROM . '>',
        'Reply-To' => MAIL_REPLY_TO,
        'MIME-Version' => '1.0',
        'Content-Type' => 'text/html; charset=UTF-8',
        'X-Mailer' => 'PHP/' . phpversion()
    ];

    $headerString = '';
    foreach ($headers as $key => $value) {
        $headerString .= "$key: $value\r\n";
    }

    // HTML形式のメール本文を生成
    $htmlBody = getMailTemplate($subject, $body);

    return mail($to, $subject, $htmlBody, $headerString);
}

/**
 * メールテンプレート
 */
function getMailTemplate(string $title, string $content): string {
    return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{$title}</title>
</head>
<body style="font-family: 'Helvetica Neue', Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background: linear-gradient(135deg, #1a365d 0%, #2d5a87 100%); padding: 30px; text-align: center; border-radius: 8px 8px 0 0;">
        <h1 style="color: #fff; margin: 0; font-size: 24px;">MedCareer Bridge</h1>
        <p style="color: #b8d4e8; margin: 10px 0 0; font-size: 14px;">将来譲渡特約付き・クリニック院長就職支援サービス</p>
    </div>
    <div style="background: #fff; padding: 30px; border: 1px solid #e0e0e0; border-top: none; border-radius: 0 0 8px 8px;">
        {$content}
    </div>
    <div style="text-align: center; padding: 20px; color: #666; font-size: 12px;">
        <p>このメールは MedCareer Bridge から自動送信されています。</p>
        <p>&copy; 2026 MedCareer Bridge. All rights reserved.</p>
    </div>
</body>
</html>
HTML;
}
