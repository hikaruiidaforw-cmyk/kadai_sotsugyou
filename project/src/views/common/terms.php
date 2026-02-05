<?php
/**
 * 利用規約
 */
$pageTitle = '利用規約';

require_once __DIR__ . '/../layouts/header.php';
?>

<style>
.terms-page {
    padding: var(--space-3xl) 0 var(--space-4xl);
    background: var(--color-white);
}

.terms-container {
    max-width: 800px;
    margin: 0 auto;
}

.terms-title {
    font-size: 2rem;
    margin-bottom: var(--space-2xl);
    text-align: center;
}

.terms-section {
    margin-bottom: var(--space-2xl);
}

.terms-section-title {
    font-size: 1.25rem;
    margin-bottom: var(--space-md);
    color: var(--color-primary);
}

.terms-content {
    line-height: 1.8;
    color: var(--color-gray-700);
}

.terms-content p {
    margin-bottom: var(--space-md);
}

.terms-content ul {
    margin: var(--space-md) 0;
    padding-left: var(--space-lg);
}

.terms-content li {
    margin-bottom: var(--space-sm);
}
</style>

<div class="terms-page">
    <div class="container terms-container">
        <h1 class="terms-title">利用規約</h1>

        <div class="terms-content">
            <p>この利用規約（以下「本規約」）は、MedCareer Bridge（以下「当サービス」）の利用条件を定めるものです。</p>

            <div class="terms-section">
                <h2 class="terms-section-title">第1条（適用）</h2>
                <p>本規約は、ユーザーと当サービス運営者との間の当サービスの利用に関わる一切の関係に適用されるものとします。</p>
            </div>

            <div class="terms-section">
                <h2 class="terms-section-title">第2条（利用登録）</h2>
                <p>登録希望者が当サービス所定の方法によって利用登録を申請し、当サービスがこれを承認することによって、利用登録が完了するものとします。</p>
                <p>当サービスは、以下の場合には利用登録の申請を承認しないことがあります。</p>
                <ul>
                    <li>虚偽の事項を届け出た場合</li>
                    <li>本規約に違反したことがある者からの申請である場合</li>
                    <li>その他、当サービスが利用登録を相当でないと判断した場合</li>
                </ul>
            </div>

            <div class="terms-section">
                <h2 class="terms-section-title">第3条（禁止事項）</h2>
                <p>ユーザーは、当サービスの利用にあたり、以下の行為をしてはなりません。</p>
                <ul>
                    <li>法令または公序良俗に違反する行為</li>
                    <li>犯罪行為に関連する行為</li>
                    <li>当サービスのサーバーまたはネットワークの機能を破壊したり、妨害したりする行為</li>
                    <li>当サービスの運営を妨害するおそれのある行為</li>
                    <li>他のユーザーに関する個人情報等を収集または蓄積する行為</li>
                    <li>他のユーザーに成りすます行為</li>
                    <li>当サービスに関連して、反社会的勢力に対して直接または間接に利益を供与する行為</li>
                    <li>その他、当サービスが不適切と判断する行為</li>
                </ul>
            </div>

            <div class="terms-section">
                <h2 class="terms-section-title">第4条（サービス内容の変更等）</h2>
                <p>当サービスは、ユーザーに通知することなく、サービスの内容を変更しまたはサービスの提供を中止することができるものとし、これによってユーザーに生じた損害について一切の責任を負いません。</p>
            </div>

            <div class="terms-section">
                <h2 class="terms-section-title">第5条（個人情報の取扱い）</h2>
                <p>当サービスは、当サービスの利用によって取得する個人情報については、当サービス「プライバシーポリシー」に従い適切に取り扱うものとします。</p>
            </div>

            <div class="terms-section">
                <h2 class="terms-section-title">第6条（免責事項）</h2>
                <p>当サービスは、当サービスに事実上または法律上の瑕疵がないことを明示的にも黙示的にも保証しておりません。</p>
                <p>当サービスは、当サービスに起因してユーザーに生じたあらゆる損害について一切の責任を負いません。</p>
            </div>

            <div class="terms-section">
                <h2 class="terms-section-title">第7条（規約の変更）</h2>
                <p>当サービスは、必要と判断した場合には、ユーザーに通知することなくいつでも本規約を変更することができるものとします。</p>
            </div>

            <div class="terms-section">
                <h2 class="terms-section-title">第8条（準拠法・裁判管轄）</h2>
                <p>本規約の解釈にあたっては、日本法を準拠法とします。</p>
                <p>当サービスに関して紛争が生じた場合には、東京地方裁判所を専属的合意管轄とします。</p>
            </div>

            <p style="text-align: right; margin-top: var(--space-2xl); color: var(--color-gray-500);">
                制定日: 2026年2月1日
            </p>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
