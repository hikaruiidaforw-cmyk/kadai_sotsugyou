<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo e(SITE_DESCRIPTION); ?>">
    <title><?php echo isset($pageTitle) ? e($pageTitle) . ' | ' : ''; ?><?php echo e(SITE_NAME); ?></title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <!-- Styles -->
    <link rel="stylesheet" href="<?php echo BASE_PATH; ?>/css/common.css">
    <?php if (isset($extraCss)): ?>
        <?php foreach ($extraCss as $css): ?>
            <link rel="stylesheet" href="<?php echo e($css); ?>">
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><rect width='100' height='100' rx='16' fill='%231e3a5f'/><text x='50%' y='55%' dominant-baseline='middle' text-anchor='middle' fill='white' font-size='32' font-family='system-ui' font-weight='bold'>Dr</text></svg>">
</head>
<body>
    <header class="header" id="header">
        <div class="container">
            <div class="header-inner">
                <a href="<?php echo BASE_PATH; ?>/" class="logo">
                    <div class="logo-icon">Dr</div>
                    <span>Dr. Option</span>
                </a>

                <nav class="nav">
                    <?php if (!Auth::check()): ?>
                        <a href="<?php echo BASE_PATH; ?>/?page=home#service" class="nav-link">サービス紹介</a>
                        <a href="<?php echo BASE_PATH; ?>/?page=home#flow" class="nav-link">ご利用の流れ</a>
                        <a href="<?php echo BASE_PATH; ?>/?page=home#jobs" class="nav-link">求人情報</a>
                    <?php elseif (Auth::role() === ROLE_DOCTOR): ?>
                        <a href="<?php echo BASE_PATH; ?>/?page=doctor/dashboard" class="nav-link">ダッシュボード</a>
                        <a href="<?php echo BASE_PATH; ?>/?page=doctor/jobs" class="nav-link">求人検索</a>
                        <a href="<?php echo BASE_PATH; ?>/?page=doctor/applications" class="nav-link">応募管理</a>
                        <a href="<?php echo BASE_PATH; ?>/?page=doctor/favorites" class="nav-link">お気に入り</a>
                    <?php elseif (Auth::role() === ROLE_CLINIC): ?>
                        <a href="<?php echo BASE_PATH; ?>/?page=clinic/dashboard" class="nav-link">ダッシュボード</a>
                        <a href="<?php echo BASE_PATH; ?>/?page=clinic/jobs" class="nav-link">求人管理</a>
                        <a href="<?php echo BASE_PATH; ?>/?page=clinic/applications" class="nav-link">応募者管理</a>
                    <?php elseif (Auth::role() === ROLE_ADMIN): ?>
                        <a href="<?php echo BASE_PATH; ?>/?page=admin/dashboard" class="nav-link">ダッシュボード</a>
                        <a href="<?php echo BASE_PATH; ?>/?page=admin/doctors" class="nav-link">医師管理</a>
                        <a href="<?php echo BASE_PATH; ?>/?page=admin/clinics" class="nav-link">法人管理</a>
                        <a href="<?php echo BASE_PATH; ?>/?page=admin/jobs" class="nav-link">求人管理</a>
                    <?php endif; ?>
                </nav>

                <div class="nav-actions">
                    <?php if (!Auth::check()): ?>
                        <a href="<?php echo BASE_PATH; ?>/?page=login" class="btn btn-ghost">ログイン</a>
                        <a href="<?php echo BASE_PATH; ?>/?page=register" class="btn btn-primary">無料登録</a>
                    <?php else: ?>
                        <div class="user-menu">
                            <span class="text-sm text-gray"><?php echo e(Auth::user()['name']); ?></span>
                            <?php
                                $profilePage = match(Auth::role()) {
                                    ROLE_DOCTOR => 'doctor/profile',
                                    ROLE_CLINIC => 'clinic/profile',
                                    default => 'admin/dashboard'
                                };
                            ?>
                            <a href="<?php echo BASE_PATH; ?>/?page=<?php echo $profilePage; ?>" class="btn btn-ghost btn-sm">マイページ</a>
                            <a href="<?php echo BASE_PATH; ?>/?page=logout" class="btn btn-ghost btn-sm">ログアウト</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>

    <main style="padding-top: 64px;">
