<?php
/**
 * トップページ
 */
$pageTitle = 'トップページ';

require_once __DIR__ . '/../../models/Job.php';
$jobModel = new Job();
$latestJobs = $jobModel->getLatest(6);

require_once __DIR__ . '/../layouts/header.php';
?>

<style>
/* Header Override for Home Page */
.header {
    background: transparent;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.header.scrolled {
    background: rgba(255, 255, 255, 0.98);
    backdrop-filter: blur(12px);
    border-bottom: 1px solid var(--color-gray-100);
}

.header:not(.scrolled) .logo {
    color: #fff;
}

.header:not(.scrolled) .logo-icon {
    background: rgba(255, 255, 255, 0.15);
    border: 1px solid rgba(255, 255, 255, 0.25);
}

.header:not(.scrolled) .nav-link {
    color: rgba(255, 255, 255, 0.85);
}

.header:not(.scrolled) .nav-link:hover {
    color: #fff;
    background: rgba(255, 255, 255, 0.1);
}

.header:not(.scrolled) .btn-ghost {
    color: rgba(255, 255, 255, 0.85);
}

.header:not(.scrolled) .btn-ghost:hover {
    color: #fff;
    background: rgba(255, 255, 255, 0.1);
}

.header:not(.scrolled) .btn-primary {
    background: #fff;
    color: #1e3a5f;
}

.header:not(.scrolled) .btn-primary:hover {
    background: #f1f5f9;
}

/* Hero Section */
.hero {
    min-height: calc(100vh - 64px);
    display: flex;
    align-items: center;
    position: relative;
    overflow: hidden;
    background: #1a2a3a;
}

.hero-bg {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 0;
}

.hero-bg img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    opacity: 0.85;
}

.hero-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, rgba(26, 42, 58, 0.85) 0%, rgba(26, 42, 58, 0.6) 50%, rgba(26, 42, 58, 0.75) 100%);
    z-index: 1;
}

.hero-content {
    position: relative;
    z-index: 2;
    text-align: center;
    max-width: 800px;
    margin: 0 auto;
    padding: var(--space-8) 0;
}

.hero-badge {
    display: inline-block;
    padding: 0.5rem 1.25rem;
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.25);
    border-radius: var(--radius-full);
    font-size: 0.875rem;
    font-weight: 500;
    color: #fff;
    margin-bottom: var(--space-6);
    letter-spacing: 0.05em;
}

.hero-title {
    font-size: 3rem;
    font-weight: 700;
    line-height: 1.3;
    letter-spacing: -0.02em;
    color: #fff;
    margin-bottom: var(--space-6);
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

.hero-title span {
    color: #60a5fa;
}

.hero-description {
    font-size: 1.125rem;
    line-height: 1.9;
    color: rgba(255, 255, 255, 0.9);
    margin-bottom: var(--space-10);
    max-width: 640px;
    margin-left: auto;
    margin-right: auto;
}

.hero-cta {
    display: flex;
    gap: var(--space-4);
    justify-content: center;
    flex-wrap: wrap;
}

.hero-cta .btn-primary {
    background: #3b82f6;
    padding: 1rem 2rem;
    font-size: 1rem;
    box-shadow: 0 4px 14px rgba(59, 130, 246, 0.4);
}

.hero-cta .btn-primary:hover {
    background: #2563eb;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(59, 130, 246, 0.5);
}

.hero-cta .btn-secondary {
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.3);
    color: #fff;
    padding: 1rem 2rem;
    font-size: 1rem;
    backdrop-filter: blur(10px);
}

.hero-cta .btn-secondary:hover {
    background: rgba(255, 255, 255, 0.2);
    border-color: rgba(255, 255, 255, 0.5);
    color: #fff;
}

.hero-stats {
    display: flex;
    justify-content: center;
    gap: var(--space-12);
    margin-top: var(--space-12);
    padding-top: var(--space-8);
    border-top: 1px solid rgba(255, 255, 255, 0.15);
}

.hero-stat {
    text-align: center;
}

.hero-stat-value {
    font-size: 2.5rem;
    font-weight: 700;
    color: #fff;
    line-height: 1;
    margin-bottom: var(--space-2);
}

.hero-stat-value span {
    font-size: 1.25rem;
    font-weight: 500;
}

.hero-stat-label {
    font-size: 0.875rem;
    color: rgba(255, 255, 255, 0.7);
}

/* Features Section */
.features {
    padding: var(--space-24) 0;
    background: #f8fafc;
}

.section-header {
    text-align: center;
    max-width: 640px;
    margin: 0 auto var(--space-16);
}

.section-label {
    display: inline-block;
    padding: 0.375rem 1rem;
    background: linear-gradient(135deg, #1e3a5f 0%, #2d4a6f 100%);
    color: #fff;
    border-radius: var(--radius-full);
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    margin-bottom: var(--space-5);
}

.section-title {
    font-size: 2.25rem;
    font-weight: 700;
    letter-spacing: -0.02em;
    margin-bottom: var(--space-4);
    color: #1e3a5f;
}

.section-description {
    color: #4b5563;
    font-size: 1.0625rem;
    line-height: 1.8;
}

.features-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: var(--space-6);
}

.feature-card {
    padding: var(--space-8);
    background: #fff;
    border-radius: var(--radius-xl);
    border: none;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
    transition: all var(--transition-base);
    position: relative;
    overflow: hidden;
}

.feature-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 4px;
    background: linear-gradient(90deg, #1e3a5f 0%, #3b82f6 100%);
    transform: scaleX(0);
    transform-origin: left;
    transition: transform 0.3s ease;
}

.feature-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

.feature-card:hover::before {
    transform: scaleX(1);
}

.feature-icon {
    width: 56px;
    height: 56px;
    margin-bottom: var(--space-5);
    background: linear-gradient(135deg, #1e3a5f 0%, #2d4a6f 100%);
    border-radius: var(--radius-lg);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: #fff;
    font-weight: 700;
}

.feature-title {
    font-size: 1.125rem;
    font-weight: 600;
    margin-bottom: var(--space-3);
    color: #1e3a5f;
}

.feature-description {
    color: #4b5563;
    font-size: 0.9375rem;
    line-height: 1.8;
}

/* How It Works */
.how-it-works {
    padding: var(--space-24) 0;
    background: #fff;
}

.steps {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: var(--space-4);
}

.step {
    text-align: center;
    position: relative;
}

.step::after {
    content: '';
    position: absolute;
    top: 32px;
    left: calc(50% + 36px);
    width: calc(100% - 72px);
    height: 3px;
    background: linear-gradient(90deg, #1e3a5f 0%, #3b82f6 100%);
    opacity: 0.3;
}

.step:last-child::after {
    display: none;
}

.step-number {
    width: 64px;
    height: 64px;
    margin: 0 auto var(--space-5);
    background: linear-gradient(135deg, #1e3a5f 0%, #2d4a6f 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    font-weight: 700;
    color: #fff;
    position: relative;
    z-index: 1;
    transition: all var(--transition-base);
    box-shadow: 0 4px 14px rgba(30, 58, 95, 0.3);
}

.step:hover .step-number {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    transform: scale(1.1);
    box-shadow: 0 8px 20px rgba(59, 130, 246, 0.4);
}

.step-title {
    font-size: 1rem;
    font-weight: 600;
    margin-bottom: var(--space-2);
    color: #1e3a5f;
}

.step-description {
    color: #6b7280;
    font-size: 0.875rem;
    line-height: 1.6;
}

/* Jobs Section */
.jobs-section {
    padding: var(--space-24) 0;
    background: #f8fafc;
}

.jobs-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: var(--space-5);
}

.job-card {
    display: flex;
    gap: var(--space-5);
    padding: var(--space-6);
    background: #fff;
    border: none;
    border-radius: var(--radius-xl);
    transition: all var(--transition-base);
    text-decoration: none;
    color: inherit;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.04);
    position: relative;
    overflow: hidden;
}

.job-card::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    height: 100%;
    width: 4px;
    background: linear-gradient(180deg, #1e3a5f 0%, #3b82f6 100%);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.job-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 20px rgba(0, 0, 0, 0.08);
}

.job-card:hover::before {
    opacity: 1;
}

.job-logo {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, #1e3a5f 0%, #2d4a6f 100%);
    border-radius: var(--radius-lg);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-size: 1.375rem;
    font-weight: 600;
    color: #fff;
}

.job-content {
    flex: 1;
    min-width: 0;
}

.job-title {
    font-size: 1rem;
    font-weight: 600;
    color: #1e3a5f;
    margin-bottom: var(--space-2);
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.job-meta {
    display: flex;
    flex-wrap: wrap;
    gap: var(--space-3);
    margin-bottom: var(--space-3);
    font-size: 0.875rem;
    color: #6b7280;
}

.job-tags {
    display: flex;
    gap: var(--space-2);
    flex-wrap: wrap;
}

.job-tag {
    padding: 0.25rem 0.625rem;
    background: #e0f2fe;
    color: #0369a1;
    border-radius: var(--radius-full);
    font-size: 0.75rem;
    font-weight: 500;
}

.jobs-cta {
    text-align: center;
    margin-top: var(--space-10);
}

.jobs-cta .btn-primary {
    background: linear-gradient(135deg, #1e3a5f 0%, #2d4a6f 100%);
    padding: 1rem 2.5rem;
    font-size: 1rem;
}

.jobs-cta .btn-primary:hover {
    background: linear-gradient(135deg, #2d4a6f 0%, #3b82f6 100%);
}

/* CTA Section */
.cta-section {
    padding: var(--space-24) 0;
    background: linear-gradient(135deg, #1e3a5f 0%, #0f172a 100%);
    position: relative;
    overflow: hidden;
}

.cta-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
}

.cta-content {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: var(--space-8);
    position: relative;
    z-index: 1;
}

.cta-card {
    background: rgba(255, 255, 255, 0.05);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: var(--radius-2xl);
    padding: var(--space-10);
    text-align: center;
    transition: all var(--transition-base);
}

.cta-card:hover {
    background: rgba(255, 255, 255, 0.1);
    border-color: rgba(255, 255, 255, 0.2);
    transform: translateY(-4px);
}

.cta-card-icon {
    width: 72px;
    height: 72px;
    margin: 0 auto var(--space-6);
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    border-radius: var(--radius-xl);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    color: #fff;
    box-shadow: 0 8px 20px rgba(59, 130, 246, 0.3);
}

.cta-card-title {
    color: #fff;
    font-size: 1.375rem;
    font-weight: 600;
    margin-bottom: var(--space-4);
}

.cta-card-description {
    color: rgba(255, 255, 255, 0.7);
    font-size: 1rem;
    margin-bottom: var(--space-8);
    line-height: 1.8;
}

.cta-card .btn-primary {
    background: #fff;
    color: #1e3a5f;
    padding: 0.875rem 2rem;
    font-size: 1rem;
    font-weight: 600;
}

.cta-card .btn-primary:hover {
    background: #f1f5f9;
    color: #1e3a5f;
}

/* Responsive */
@media (max-width: 1024px) {
    .hero-title {
        font-size: 2.5rem;
    }

    .hero-stats {
        gap: var(--space-8);
    }

    .hero-stat-value {
        font-size: 2rem;
    }

    .features-grid {
        grid-template-columns: 1fr;
    }

    .steps {
        grid-template-columns: repeat(3, 1fr);
        gap: var(--space-6);
    }

    .step:nth-child(3)::after,
    .step:nth-child(5)::after {
        display: none;
    }

    .jobs-grid {
        grid-template-columns: 1fr;
    }

    .cta-content {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .hero {
        min-height: auto;
        padding: var(--space-16) 0;
    }

    .hero-title {
        font-size: 1.875rem;
    }

    .hero-description {
        font-size: 1rem;
    }

    .hero-stats {
        flex-direction: column;
        gap: var(--space-6);
    }

    .hero-stat-value {
        font-size: 1.75rem;
    }

    .section-title {
        font-size: 1.5rem;
    }

    .steps {
        grid-template-columns: 1fr;
        gap: var(--space-6);
    }

    .step::after {
        display: none;
    }

    .step-number {
        width: 56px;
        height: 56px;
        font-size: 1.125rem;
    }
}
</style>

<!-- Hero Section -->
<section class="hero">
    <div class="hero-bg">
        <img src="<?php echo BASE_PATH; ?>/images/Gemini_Generated_Image_9ap6gi9ap6gi9ap6.png" alt="医療業界のプロフェッショナル">
    </div>
    <div class="hero-overlay"></div>
    <div class="container">
        <div class="hero-content">
            <div class="hero-badge">将来譲渡特約付き院長ポジション</div>
            <h1 class="hero-title">
                新しい<span>キャリアオプション</span>を<br>提供します
            </h1>
            <p class="hero-description">
                雇われ院長から始める、確実な独立への道。リスクを抑えながら経営を学び、患者様との信頼を築き、将来の独立開業を実現する新しいキャリアパスです。
            </p>
            <div class="hero-cta">
                <a href="<?php echo BASE_PATH; ?>/?page=register/doctor" class="btn btn-primary btn-lg">医師の方はこちら</a>
                <a href="<?php echo BASE_PATH; ?>/?page=register/clinic" class="btn btn-secondary btn-lg">法人の方はこちら</a>
            </div>
            <div class="hero-stats">
                <div class="hero-stat">
                    <div class="hero-stat-value">500<span>+</span></div>
                    <div class="hero-stat-label">成約実績</div>
                </div>
                <div class="hero-stat">
                    <div class="hero-stat-value">98<span>%</span></div>
                    <div class="hero-stat-label">満足度</div>
                </div>
                <div class="hero-stat">
                    <div class="hero-stat-value">15<span>年</span></div>
                    <div class="hero-stat-label">業界経験</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="features" id="service">
    <div class="container">
        <div class="section-header">
            <span class="section-label">Features</span>
            <h2 class="section-title">サービスの特徴</h2>
            <p class="section-description">
                将来の独立開業を目指す医師と、後継者を求める医療法人をWin-Winの関係でつなぐ新しい仕組みです。
            </p>
        </div>

        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">1</div>
                <h3 class="feature-title">低リスクで開業準備</h3>
                <p class="feature-description">
                    自己資金なしで院長経験を積み、経営ノウハウを習得。開業に伴うリスクを大幅に軽減できます。
                </p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">2</div>
                <h3 class="feature-title">経営を実践で学ぶ</h3>
                <p class="feature-description">
                    実際のクリニック運営を通じて、スタッフ管理、集患、経営判断など実践的なスキルを習得できます。
                </p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">3</div>
                <h3 class="feature-title">確実な事業承継</h3>
                <p class="feature-description">
                    契約に基づく明確な譲渡条件。既存の患者基盤とスタッフを引き継ぎ、スムーズな独立が可能です。
                </p>
            </div>
        </div>
    </div>
</section>

<!-- How It Works -->
<section class="how-it-works" id="flow">
    <div class="container">
        <div class="section-header">
            <span class="section-label">Process</span>
            <h2 class="section-title">ご利用の流れ</h2>
            <p class="section-description">
                会員登録から独立開業まで、専門スタッフがサポートいたします。
            </p>
        </div>

        <div class="steps">
            <div class="step">
                <div class="step-number">1</div>
                <h4 class="step-title">会員登録</h4>
                <p class="step-description">無料で簡単に登録</p>
            </div>
            <div class="step">
                <div class="step-number">2</div>
                <h4 class="step-title">求人検索</h4>
                <p class="step-description">条件に合った求人を探す</p>
            </div>
            <div class="step">
                <div class="step-number">3</div>
                <h4 class="step-title">面接・選考</h4>
                <p class="step-description">法人との面接</p>
            </div>
            <div class="step">
                <div class="step-number">4</div>
                <h4 class="step-title">契約・就任</h4>
                <p class="step-description">院長に就任</p>
            </div>
            <div class="step">
                <div class="step-number">5</div>
                <h4 class="step-title">独立開業</h4>
                <p class="step-description">クリニックを譲り受け</p>
            </div>
        </div>
    </div>
</section>

<!-- Jobs Section -->
<section class="jobs-section" id="jobs">
    <div class="container">
        <div class="section-header">
            <span class="section-label">Jobs</span>
            <h2 class="section-title">新着求人情報</h2>
            <p class="section-description">
                将来譲渡特約付きの院長ポジションをご紹介します。
            </p>
        </div>

        <div class="jobs-grid">
            <?php if (empty($latestJobs)): ?>
                <div class="text-center text-gray" style="grid-column: 1/-1; padding: var(--space-12);">
                    現在、求人情報はありません。
                </div>
            <?php else: ?>
                <?php foreach ($latestJobs as $job): ?>
                    <a href="<?php echo BASE_PATH; ?>/?page=doctor/job&id=<?php echo $job['id']; ?>" class="job-card">
                        <div class="job-logo">
                            <?php echo mb_substr($job['facility_name'], 0, 1); ?>
                        </div>
                        <div class="job-content">
                            <h3 class="job-title"><?php echo e($job['title']); ?></h3>
                            <div class="job-meta">
                                <span><?php echo e($job['prefecture']); ?></span>
                                <span>|</span>
                                <span>年収 <?php echo number_format($job['salary_min']); ?>〜<?php echo number_format($job['salary_max']); ?>万円</span>
                            </div>
                            <div class="job-tags">
                                <?php if ($job['specialty_names']): ?>
                                    <?php foreach (explode(', ', $job['specialty_names']) as $specialty): ?>
                                        <span class="job-tag"><?php echo e($specialty); ?></span>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="jobs-cta">
            <a href="<?php echo BASE_PATH; ?>/?page=doctor/jobs" class="btn btn-primary btn-lg">すべての求人を見る</a>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section">
    <div class="container">
        <div class="cta-content">
            <div class="cta-card">
                <div class="cta-card-icon">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                </div>
                <h3 class="cta-card-title">医師の方へ</h3>
                <p class="cta-card-description">
                    将来の独立開業を目指しながら、<br>
                    リスクを抑えて経営経験を積みませんか？
                </p>
                <a href="<?php echo BASE_PATH; ?>/?page=register/doctor" class="btn btn-primary btn-lg">無料で登録する</a>
            </div>
            <div class="cta-card">
                <div class="cta-card-icon">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                        <polyline points="9 22 9 12 15 12 15 22"></polyline>
                    </svg>
                </div>
                <h3 class="cta-card-title">医療法人の方へ</h3>
                <p class="cta-card-description">
                    後継者問題・管理医師不足を解決する<br>
                    新しいマッチングサービスです。
                </p>
                <a href="<?php echo BASE_PATH; ?>/?page=register/clinic" class="btn btn-primary btn-lg">法人登録する</a>
            </div>
        </div>
    </div>
</section>

<script>
// Header scroll effect
document.addEventListener('DOMContentLoaded', function() {
    const header = document.getElementById('header');

    function updateHeader() {
        if (window.scrollY > 50) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
    }

    window.addEventListener('scroll', updateHeader);
    updateHeader();
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
