<?php
/**
 * プライバシーポリシー
 */
$pageTitle = 'プライバシーポリシー';

require_once __DIR__ . '/../layouts/header.php';
?>

<style>
.privacy-page {
    padding: var(--space-3xl) 0 var(--space-4xl);
    background: var(--color-white);
}

.privacy-container {
    max-width: 800px;
    margin: 0 auto;
}

.privacy-title {
    font-size: 2rem;
    margin-bottom: var(--space-2xl);
    text-align: center;
}

.privacy-section {
    margin-bottom: var(--space-2xl);
}

.privacy-section-title {
    font-size: 1.25rem;
    margin-bottom: var(--space-md);
    color: var(--color-primary);
}

.privacy-content {
    line-height: 1.8;
    color: var(--color-gray-700);
}

.privacy-content p {
    margin-bottom: var(--space-md);
}

.privacy-content ul {
    margin: var(--space-md) 0;
    padding-left: var(--space-lg);
}

.privacy-content li {
    margin-bottom: var(--space-sm);
}
</style>

<div class="privacy-page">
    <div class="container privacy-container">
        <h1 class="privacy-title">プライバシーポリシー</h1>

        <div class="privacy-content">
            <p>MedCareer Bridge（以下「当サービス」）は、ユーザーの個人情報の取扱いについて、以下のとおりプライバシーポリシーを定めます。</p>

            <div class="privacy-section">
                <h2 class="privacy-section-title">1. 収集する情報</h2>
                <p>当サービスは、サービス提供のため、以下の情報を収集します。</p>
                <ul>
                    <li>氏名、生年月日、性別</li>
                    <li>メールアドレス、電話番号、住所</li>
                    <li>医師免許番号、資格情報（医師会員の場合）</li>
                    <li>法人情報、法人番号（法人会員の場合）</li>
                    <li>職歴、経歴情報</li>
                    <li>サービス利用履歴</li>
                </ul>
            </div>

            <div class="privacy-section">
                <h2 class="privacy-section-title">2. 利用目的</h2>
                <p>収集した情報は、以下の目的で利用します。</p>
                <ul>
                    <li>サービスの提供・運営</li>
                    <li>ユーザーからのお問い合わせへの対応</li>
                    <li>マッチングサービスの実施</li>
                    <li>メールマガジン・お知らせの配信</li>
                    <li>サービスの改善・新サービスの開発</li>
                    <li>利用規約に違反する行為への対応</li>
                </ul>
            </div>

            <div class="privacy-section">
                <h2 class="privacy-section-title">3. 第三者への提供</h2>
                <p>当サービスは、以下の場合を除き、ユーザーの同意なく個人情報を第三者に提供することはありません。</p>
                <ul>
                    <li>法令に基づく場合</li>
                    <li>人の生命、身体または財産の保護のために必要がある場合</li>
                    <li>マッチングサービスにおいて、求人・求職者間で必要な情報を共有する場合</li>
                </ul>
            </div>

            <div class="privacy-section">
                <h2 class="privacy-section-title">4. 安全管理措置</h2>
                <p>当サービスは、個人情報の漏洩、滅失またはき損の防止その他の個人情報の安全管理のために必要かつ適切な措置を講じます。</p>
            </div>

            <div class="privacy-section">
                <h2 class="privacy-section-title">5. 個人情報の開示・訂正・削除</h2>
                <p>ユーザーは、当サービスに対して、自己の個人情報の開示、訂正、削除を請求することができます。請求を受けた場合、当サービスは速やかに対応いたします。</p>
            </div>

            <div class="privacy-section">
                <h2 class="privacy-section-title">6. Cookie（クッキー）の使用</h2>
                <p>当サービスは、サービスの利便性向上のため、Cookieを使用することがあります。ユーザーはブラウザの設定によりCookieの受け入れを拒否することができますが、その場合、サービスの一部が利用できなくなる可能性があります。</p>
            </div>

            <div class="privacy-section">
                <h2 class="privacy-section-title">7. プライバシーポリシーの変更</h2>
                <p>当サービスは、必要に応じて、本プライバシーポリシーを変更することがあります。変更後のプライバシーポリシーは、当サービスのウェブサイトに掲載した時点から効力を生じるものとします。</p>
            </div>

            <div class="privacy-section">
                <h2 class="privacy-section-title">8. お問い合わせ</h2>
                <p>本プライバシーポリシーに関するお問い合わせは、以下の連絡先までお願いいたします。</p>
                <p>Email: privacy@medcareer-bridge.jp</p>
            </div>

            <p style="text-align: right; margin-top: var(--space-2xl); color: var(--color-gray-500);">
                制定日: 2026年2月1日
            </p>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
