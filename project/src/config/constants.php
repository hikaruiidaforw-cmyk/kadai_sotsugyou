<?php
/**
 * 定数定義
 */

// サイト情報
define('SITE_NAME', 'Dr. Option');
define('SITE_URL', 'https://gs-iidahikaru.sakura.ne.jp/kadai/project/public');
define('SITE_DESCRIPTION', '将来譲渡特約付き・クリニック院長就職支援サービス');

// ベースパス（リンク用）
define('BASE_PATH', '/kadai/project/public');

// ファイルパス
define('ROOT_PATH', dirname(dirname(__DIR__)));
define('PUBLIC_PATH', ROOT_PATH . '/public');
define('UPLOAD_PATH', PUBLIC_PATH . '/uploads');
define('STORAGE_PATH', ROOT_PATH . '/storage');

// ファイルアップロード設定
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('MAX_IMAGE_SIZE', 2 * 1024 * 1024); // 2MB
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/gif']);
define('ALLOWED_DOC_TYPES', ['application/pdf']);

// ページネーション
define('ITEMS_PER_PAGE', 20);

// セッション設定
define('SESSION_LIFETIME', 86400); // 24時間

// ユーザーロール
define('ROLE_DOCTOR', 'doctor');
define('ROLE_CLINIC', 'clinic');
define('ROLE_ADMIN', 'admin');

// ユーザーステータス
define('STATUS_PENDING', 'pending');
define('STATUS_ACTIVE', 'active');
define('STATUS_SUSPENDED', 'suspended');

// 応募ステータス
define('APPLICATION_STATUS', [
    'applied' => '応募中',
    'document_screening' => '書類選考中',
    'interview_scheduling' => '面接調整中',
    'interview_completed' => '面接完了',
    'offered' => '内定',
    'accepted' => '承諾',
    'declined' => '辞退',
    'rejected' => '不採用'
]);

// 求人ステータス
define('JOB_STATUS', [
    'draft' => '下書き',
    'pending' => '承認待ち',
    'published' => '公開中',
    'closed' => '募集終了'
]);

// 都道府県
define('PREFECTURES', [
    '北海道', '青森県', '岩手県', '宮城県', '秋田県', '山形県', '福島県',
    '茨城県', '栃木県', '群馬県', '埼玉県', '千葉県', '東京都', '神奈川県',
    '新潟県', '富山県', '石川県', '福井県', '山梨県', '長野県', '岐阜県',
    '静岡県', '愛知県', '三重県', '滋賀県', '京都府', '大阪府', '兵庫県',
    '奈良県', '和歌山県', '鳥取県', '島根県', '岡山県', '広島県', '山口県',
    '徳島県', '香川県', '愛媛県', '高知県', '福岡県', '佐賀県', '長崎県',
    '熊本県', '大分県', '宮崎県', '鹿児島県', '沖縄県'
]);
