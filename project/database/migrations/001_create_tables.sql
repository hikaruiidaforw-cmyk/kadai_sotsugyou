-- クリニック院長就職支援サービス データベース作成
-- Version: 1.0
--
-- 【使用方法】
-- 1. PHPMyAdminで「新規作成」からデータベース「clinic_job_platform」を作成
--    （照合順序: utf8mb4_unicode_ci）
-- 2. 作成したデータベースを選択した状態でこのファイルをインポート
-- 3. その後、001_initial_data.sql をインポート

-- ユーザー基本情報
CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('doctor', 'clinic', 'admin') NOT NULL,
    status ENUM('pending', 'active', 'suspended') DEFAULT 'pending',
    email_verified_at DATETIME NULL,
    remember_token VARCHAR(100) NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_role_status (role, status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 診療科目マスタ
CREATE TABLE IF NOT EXISTS specialties (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    sort_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 医師情報
CREATE TABLE IF NOT EXISTS doctors (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL UNIQUE,
    last_name VARCHAR(50) NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name_kana VARCHAR(50) NOT NULL,
    first_name_kana VARCHAR(50) NOT NULL,
    birth_date DATE NOT NULL,
    gender ENUM('male', 'female', 'other') NOT NULL,
    phone VARCHAR(20) NOT NULL,
    postal_code VARCHAR(10) NULL,
    prefecture VARCHAR(10) NULL,
    address TEXT NULL,
    license_number VARCHAR(20) NOT NULL,
    license_date DATE NOT NULL,
    profile_photo VARCHAR(255) NULL,
    resume_file VARCHAR(255) NULL,
    self_introduction TEXT NULL,
    desired_regions JSON NULL,
    desired_salary_min INT UNSIGNED NULL,
    desired_salary_max INT UNSIGNED NULL,
    desired_opening_year INT UNSIGNED NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_license (license_number)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 医師専門科目
CREATE TABLE IF NOT EXISTS doctor_specialties (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    doctor_id INT UNSIGNED NOT NULL,
    specialty_id INT UNSIGNED NOT NULL,
    is_main BOOLEAN DEFAULT FALSE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE,
    FOREIGN KEY (specialty_id) REFERENCES specialties(id),
    UNIQUE KEY unique_doctor_specialty (doctor_id, specialty_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 医師職歴
CREATE TABLE IF NOT EXISTS doctor_careers (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    doctor_id INT UNSIGNED NOT NULL,
    facility_name VARCHAR(100) NOT NULL,
    department VARCHAR(50) NULL,
    position VARCHAR(50) NULL,
    start_date DATE NOT NULL,
    end_date DATE NULL,
    description TEXT NULL,
    sort_order INT DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE,
    INDEX idx_doctor (doctor_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 医療法人情報
CREATE TABLE IF NOT EXISTS clinics (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL UNIQUE,
    corp_name VARCHAR(100) NOT NULL,
    corp_number VARCHAR(13) NOT NULL,
    representative_name VARCHAR(50) NOT NULL,
    established_date DATE NULL,
    postal_code VARCHAR(10) NOT NULL,
    prefecture VARCHAR(10) NOT NULL,
    address TEXT NOT NULL,
    phone VARCHAR(20) NOT NULL,
    website_url VARCHAR(255) NULL,
    business_description TEXT NULL,
    facility_count INT UNSIGNED DEFAULT 1,
    employee_count INT UNSIGNED NULL,
    annual_revenue VARCHAR(50) NULL,
    introduction TEXT NULL,
    logo_image VARCHAR(255) NULL,
    contact_person_name VARCHAR(50) NOT NULL,
    contact_person_position VARCHAR(50) NULL,
    contact_person_email VARCHAR(255) NOT NULL,
    contact_person_phone VARCHAR(20) NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_corp_number (corp_number),
    INDEX idx_prefecture (prefecture)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 求人情報
CREATE TABLE IF NOT EXISTS jobs (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    clinic_id INT UNSIGNED NOT NULL,
    title VARCHAR(100) NOT NULL,
    facility_name VARCHAR(100) NOT NULL,
    postal_code VARCHAR(10) NOT NULL,
    prefecture VARCHAR(10) NOT NULL,
    address TEXT NOT NULL,
    description TEXT NOT NULL,
    work_hours TEXT NOT NULL,
    salary_min INT UNSIGNED NULL,
    salary_max INT UNSIGNED NULL,
    salary_description TEXT NULL,
    benefits TEXT NULL,
    requirements TEXT NULL,
    recruitment_count INT UNSIGNED DEFAULT 1,
    application_deadline DATE NULL,

    -- 譲渡特約条件
    transfer_min_tenure_months INT UNSIGNED NOT NULL COMMENT '最低勤務期間（月）',
    transfer_performance_target TEXT NULL COMMENT '業績目標',
    transfer_price_type ENUM('fixed', 'formula') NOT NULL COMMENT '価格タイプ',
    transfer_price_fixed INT UNSIGNED NULL COMMENT '固定価格（万円）',
    transfer_price_formula TEXT NULL COMMENT '算定方式',
    transfer_scope TEXT NOT NULL COMMENT '譲渡対象範囲',
    transfer_option_deadline DATE NULL COMMENT 'オプション期限',
    transfer_other_conditions TEXT NULL COMMENT 'その他条件',

    status ENUM('draft', 'pending', 'published', 'closed') DEFAULT 'draft',
    published_at DATETIME NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (clinic_id) REFERENCES clinics(id) ON DELETE CASCADE,
    INDEX idx_status (status),
    INDEX idx_prefecture (prefecture),
    INDEX idx_published (published_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 求人診療科目
CREATE TABLE IF NOT EXISTS job_specialties (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    job_id INT UNSIGNED NOT NULL,
    specialty_id INT UNSIGNED NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (job_id) REFERENCES jobs(id) ON DELETE CASCADE,
    FOREIGN KEY (specialty_id) REFERENCES specialties(id),
    UNIQUE KEY unique_job_specialty (job_id, specialty_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 求人写真
CREATE TABLE IF NOT EXISTS job_photos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    job_id INT UNSIGNED NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    caption VARCHAR(100) NULL,
    sort_order INT DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (job_id) REFERENCES jobs(id) ON DELETE CASCADE,
    INDEX idx_job (job_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 応募情報
CREATE TABLE IF NOT EXISTS applications (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    job_id INT UNSIGNED NOT NULL,
    doctor_id INT UNSIGNED NOT NULL,
    status ENUM(
        'applied',
        'document_screening',
        'interview_scheduling',
        'interview_completed',
        'offered',
        'accepted',
        'declined',
        'rejected'
    ) DEFAULT 'applied',
    cover_letter TEXT NULL,
    clinic_memo TEXT NULL,
    admin_memo TEXT NULL,
    applied_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    status_changed_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (job_id) REFERENCES jobs(id) ON DELETE CASCADE,
    FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE,
    UNIQUE KEY unique_application (job_id, doctor_id),
    INDEX idx_status (status),
    INDEX idx_doctor (doctor_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- メッセージ
CREATE TABLE IF NOT EXISTS messages (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    application_id INT UNSIGNED NOT NULL,
    sender_user_id INT UNSIGNED NOT NULL,
    body TEXT NOT NULL,
    attachment_file VARCHAR(255) NULL,
    is_read BOOLEAN DEFAULT FALSE,
    read_at DATETIME NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (application_id) REFERENCES applications(id) ON DELETE CASCADE,
    FOREIGN KEY (sender_user_id) REFERENCES users(id),
    INDEX idx_application (application_id),
    INDEX idx_unread (application_id, is_read)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- お気に入り
CREATE TABLE IF NOT EXISTS favorites (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    doctor_id INT UNSIGNED NOT NULL,
    job_id INT UNSIGNED NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE,
    FOREIGN KEY (job_id) REFERENCES jobs(id) ON DELETE CASCADE,
    UNIQUE KEY unique_favorite (doctor_id, job_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- パスワードリセットトークン
CREATE TABLE IF NOT EXISTS password_resets (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    token VARCHAR(100) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_token (token)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
