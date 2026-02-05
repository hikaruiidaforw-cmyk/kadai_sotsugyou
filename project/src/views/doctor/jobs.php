<?php
/**
 * 求人検索画面
 */
$pageTitle = '求人検索';

require_once __DIR__ . '/../../models/Job.php';
require_once __DIR__ . '/../../models/Specialty.php';
require_once __DIR__ . '/../../models/Favorite.php';
require_once __DIR__ . '/../../models/Doctor.php';

$jobModel = new Job();
$specialtyModel = new Specialty();
$favoriteModel = new Favorite();
$doctorModel = new Doctor();

$doctor = $doctorModel->findByUserId(Auth::id());
$specialties = $specialtyModel->getAll();

// 検索パラメータ
$filters = [
    'prefecture' => $_GET['prefecture'] ?? '',
    'specialty_id' => $_GET['specialty_id'] ?? '',
    'salary_min' => $_GET['salary_min'] ?? '',
    'salary_max' => $_GET['salary_max'] ?? '',
    'keyword' => $_GET['keyword'] ?? ''
];

$page = max(1, intval($_GET['p'] ?? 1));
$jobs = $jobModel->search($filters, $page);
$totalCount = $jobModel->searchCount($filters);
$totalPages = ceil($totalCount / ITEMS_PER_PAGE);

// お気に入りIDリスト
$favoriteJobIds = [];
if ($doctor) {
    $favorites = $favoriteModel->getByDoctorId($doctor['id'], 1, 1000);
    foreach ($favorites as $fav) {
        $favoriteJobIds[] = $fav['job_id'];
    }
}

require_once __DIR__ . '/../layouts/header.php';
?>

<style>
.jobs-page {
    padding: var(--space-2xl) 0 var(--space-4xl);
    background: var(--color-gray-50);
    min-height: calc(100vh - 72px);
}

.jobs-header {
    margin-bottom: var(--space-xl);
}

.jobs-title {
    font-size: 1.75rem;
    margin-bottom: var(--space-xs);
}

.jobs-subtitle {
    color: var(--color-gray-500);
}

/* Search Form */
.search-form {
    background: var(--color-white);
    border-radius: var(--radius-lg);
    padding: var(--space-xl);
    margin-bottom: var(--space-xl);
    border: 1px solid var(--color-gray-200);
}

.search-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: var(--space-md);
    margin-bottom: var(--space-lg);
}

.search-row {
    display: grid;
    grid-template-columns: 1fr 1fr 200px;
    gap: var(--space-md);
    align-items: end;
}

/* Job List */
.jobs-result-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: var(--space-lg);
}

.jobs-count {
    font-size: 0.9375rem;
    color: var(--color-gray-600);
}

.jobs-count strong {
    color: var(--color-primary);
    font-size: 1.25rem;
}

.jobs-list {
    display: flex;
    flex-direction: column;
    gap: var(--space-md);
}

.job-card-full {
    display: grid;
    grid-template-columns: 80px 1fr auto;
    gap: var(--space-lg);
    padding: var(--space-lg);
    background: var(--color-white);
    border: 1px solid var(--color-gray-200);
    border-radius: var(--radius-lg);
    transition: all var(--transition-base);
}

.job-card-full:hover {
    border-color: var(--color-primary-300);
    box-shadow: var(--shadow-lg);
}

.job-card-logo {
    width: 80px;
    height: 80px;
    background: var(--color-gray-100);
    border-radius: var(--radius-md);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    color: var(--color-primary);
}

.job-card-content {
    min-width: 0;
}

.job-card-title {
    font-size: 1.125rem;
    font-weight: 600;
    margin-bottom: var(--space-sm);
    color: var(--color-gray-900);
}

.job-card-title a {
    color: inherit;
    text-decoration: none;
}

.job-card-title a:hover {
    color: var(--color-primary);
}

.job-card-company {
    color: var(--color-gray-500);
    font-size: 0.9375rem;
    margin-bottom: var(--space-sm);
}

.job-card-info {
    display: flex;
    flex-wrap: wrap;
    gap: var(--space-md);
    font-size: 0.875rem;
    color: var(--color-gray-600);
    margin-bottom: var(--space-sm);
}

.job-card-info-item {
    display: flex;
    align-items: center;
    gap: var(--space-xs);
}

.job-card-tags {
    display: flex;
    gap: var(--space-xs);
    flex-wrap: wrap;
}

.job-card-actions {
    display: flex;
    flex-direction: column;
    gap: var(--space-sm);
    justify-content: center;
}

.btn-favorite {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    border: 2px solid var(--color-gray-200);
    background: white;
    color: var(--color-gray-400);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    cursor: pointer;
    transition: all var(--transition-fast);
}

.btn-favorite:hover,
.btn-favorite.active {
    border-color: var(--color-error);
    color: var(--color-error);
    background: var(--color-error-light);
}

/* Transfer Badge */
.transfer-badge {
    display: inline-flex;
    align-items: center;
    gap: var(--space-xs);
    padding: 0.5rem 0.75rem;
    background: linear-gradient(135deg, var(--color-accent-light) 0%, var(--color-accent) 100%);
    color: var(--color-gray-900);
    border-radius: var(--radius-md);
    font-size: 0.75rem;
    font-weight: 600;
}

@media (max-width: 1024px) {
    .search-grid {
        grid-template-columns: repeat(2, 1fr);
    }

    .search-row {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .search-grid {
        grid-template-columns: 1fr;
    }

    .job-card-full {
        grid-template-columns: 1fr;
    }

    .job-card-logo {
        display: none;
    }

    .job-card-actions {
        flex-direction: row;
    }
}
</style>

<div class="jobs-page">
    <div class="container">
        <div class="jobs-header">
            <h1 class="jobs-title">求人検索</h1>
            <p class="jobs-subtitle">将来譲渡特約付きの院長ポジションを探す</p>
        </div>

        <!-- 検索フォーム -->
        <form class="search-form" method="GET" action="">
            <input type="hidden" name="page" value="doctor/jobs">

            <div class="search-grid">
                <div class="form-group mb-0">
                    <label class="form-label" for="prefecture">エリア</label>
                    <select id="prefecture" name="prefecture" class="form-select">
                        <option value="">すべての地域</option>
                        <?php foreach (PREFECTURES as $pref): ?>
                            <option value="<?php echo e($pref); ?>" <?php echo $filters['prefecture'] === $pref ? 'selected' : ''; ?>>
                                <?php echo e($pref); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group mb-0">
                    <label class="form-label" for="specialty_id">診療科目</label>
                    <select id="specialty_id" name="specialty_id" class="form-select">
                        <option value="">すべての科目</option>
                        <?php foreach ($specialties as $spec): ?>
                            <option value="<?php echo $spec['id']; ?>" <?php echo $filters['specialty_id'] == $spec['id'] ? 'selected' : ''; ?>>
                                <?php echo e($spec['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group mb-0">
                    <label class="form-label" for="salary_min">年収（下限）</label>
                    <select id="salary_min" name="salary_min" class="form-select">
                        <option value="">指定なし</option>
                        <?php foreach ([1000, 1200, 1500, 1800, 2000, 2500, 3000] as $salary): ?>
                            <option value="<?php echo $salary; ?>" <?php echo $filters['salary_min'] == $salary ? 'selected' : ''; ?>>
                                <?php echo number_format($salary); ?>万円以上
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group mb-0">
                    <label class="form-label" for="salary_max">年収（上限）</label>
                    <select id="salary_max" name="salary_max" class="form-select">
                        <option value="">指定なし</option>
                        <?php foreach ([1500, 2000, 2500, 3000, 4000, 5000] as $salary): ?>
                            <option value="<?php echo $salary; ?>" <?php echo $filters['salary_max'] == $salary ? 'selected' : ''; ?>>
                                <?php echo number_format($salary); ?>万円以下
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="search-row">
                <div class="form-group mb-0">
                    <label class="form-label" for="keyword">キーワード</label>
                    <input type="text" id="keyword" name="keyword" class="form-input"
                           value="<?php echo e($filters['keyword']); ?>"
                           placeholder="施設名、業務内容など">
                </div>
                <div></div>
                <button type="submit" class="btn btn-primary btn-lg">
                    検索する
                </button>
            </div>
        </form>

        <!-- 検索結果 -->
        <div class="jobs-result-header">
            <p class="jobs-count">
                検索結果: <strong><?php echo number_format($totalCount); ?></strong>件
            </p>
        </div>

        <?php if (empty($jobs)): ?>
            <div class="card">
                <div class="card-body text-center" style="padding: var(--space-3xl);">
                    <p class="text-gray mb-2">条件に一致する求人が見つかりませんでした</p>
                    <p class="text-sm text-gray">検索条件を変更してお試しください</p>
                </div>
            </div>
        <?php else: ?>
            <div class="jobs-list">
                <?php foreach ($jobs as $job): ?>
                    <div class="job-card-full">
                        <div class="job-card-logo">
                            <?php echo mb_substr($job['facility_name'], 0, 1); ?>
                        </div>
                        <div class="job-card-content">
                            <h3 class="job-card-title">
                                <a href="<?php echo BASE_PATH; ?>/?page=doctor/job&id=<?php echo $job['id']; ?>">
                                    <?php echo e($job['title']); ?>
                                </a>
                            </h3>
                            <p class="job-card-company"><?php echo e($job['corp_name']); ?></p>
                            <div class="job-card-info">
                                <span class="job-card-info-item">📍 <?php echo e($job['prefecture']); ?></span>
                                <span class="job-card-info-item">💰 年収 <?php echo number_format($job['salary_min']); ?>〜<?php echo number_format($job['salary_max']); ?>万円</span>
                            </div>
                            <div class="job-card-tags">
                                <?php if ($job['specialty_names']): ?>
                                    <?php foreach (explode(', ', $job['specialty_names']) as $specialty): ?>
                                        <span class="badge badge-primary"><?php echo e($specialty); ?></span>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                                <span class="transfer-badge">
                                    🔑 譲渡価格: <?php echo $job['transfer_price_type'] === 'fixed' ? number_format($job['transfer_price_fixed']) . '万円' : '算定方式'; ?>
                                </span>
                            </div>
                        </div>
                        <div class="job-card-actions">
                            <a href="<?php echo BASE_PATH; ?>/?page=doctor/job&id=<?php echo $job['id']; ?>" class="btn btn-primary">
                                詳細を見る
                            </a>
                            <button type="button" class="btn-favorite <?php echo in_array($job['id'], $favoriteJobIds) ? 'active' : ''; ?>"
                                    data-job-id="<?php echo $job['id']; ?>">
                                ♥
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- ページネーション -->
            <?php if ($totalPages > 1): ?>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?page=doctor/jobs&p=<?php echo $page - 1; ?>&<?php echo http_build_query($filters); ?>" class="pagination-item">
                            ←
                        </a>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <a href="?page=doctor/jobs&p=<?php echo $i; ?>&<?php echo http_build_query($filters); ?>"
                           class="pagination-item <?php echo $i === $page ? 'active' : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>

                    <?php if ($page < $totalPages): ?>
                        <a href="?page=doctor/jobs&p=<?php echo $page + 1; ?>&<?php echo http_build_query($filters); ?>" class="pagination-item">
                            →
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
