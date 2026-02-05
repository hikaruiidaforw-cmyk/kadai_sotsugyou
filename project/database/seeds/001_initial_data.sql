-- 初期データ投入
--
-- 【使用方法】
-- PHPMyAdminで「clinic_job_platform」データベースを選択した状態でインポート

-- 診療科目マスタ
INSERT INTO specialties (name, sort_order, is_active) VALUES
('内科', 1, TRUE),
('外科', 2, TRUE),
('小児科', 3, TRUE),
('産婦人科', 4, TRUE),
('整形外科', 5, TRUE),
('皮膚科', 6, TRUE),
('眼科', 7, TRUE),
('耳鼻咽喉科', 8, TRUE),
('泌尿器科', 9, TRUE),
('精神科', 10, TRUE),
('心療内科', 11, TRUE),
('循環器内科', 12, TRUE),
('消化器内科', 13, TRUE),
('呼吸器内科', 14, TRUE),
('美容皮膚科', 15, TRUE),
('美容外科', 16, TRUE),
('形成外科', 17, TRUE),
('リハビリテーション科', 18, TRUE),
('放射線科', 19, TRUE),
('麻酔科', 20, TRUE);

-- 管理者アカウント（パスワード: Admin123!）
INSERT INTO users (email, password, role, status, email_verified_at, created_at)
VALUES (
    'admin@medcareer-bridge.jp',
    '$2y$10$Z.FcyyFob4Dy0ajcMIjEMuV/qspyEoECB0PYYxW16a54AOV6f6T56',
    'admin',
    'active',
    NOW(),
    NOW()
);

-- テスト用医師アカウント（パスワード: Doctor123!）
INSERT INTO users (email, password, role, status, email_verified_at, created_at)
VALUES (
    'doctor@example.com',
    '$2y$10$fVBUo/qdhYkVhO/LPaIBIutC27xrYZkRX44jFEXbvRV4/I6GsQ576',
    'doctor',
    'active',
    NOW(),
    NOW()
);

INSERT INTO doctors (
    user_id, last_name, first_name, last_name_kana, first_name_kana,
    birth_date, gender, phone, postal_code, prefecture, address,
    license_number, license_date, self_introduction,
    desired_salary_min, desired_salary_max, desired_opening_year
) VALUES (
    2,
    '山田', '太郎', 'ヤマダ', 'タロウ',
    '1980-05-15', 'male', '090-1234-5678',
    '150-0001', '東京都', '渋谷区神宮前1-1-1',
    '123456', '2005-04-01',
    '大学病院での10年間の勤務経験を経て、地域医療に貢献したいと考えています。将来的には自身のクリニックを持ち、患者様に寄り添った医療を提供したいと考えています。',
    1500, 2500, 2028
);

INSERT INTO doctor_specialties (doctor_id, specialty_id, is_main) VALUES
(1, 1, TRUE),
(1, 12, FALSE);

-- テスト用法人アカウント（パスワード: Clinic123!）
INSERT INTO users (email, password, role, status, email_verified_at, created_at)
VALUES (
    'clinic@example.com',
    '$2y$10$mrSYwKa5gJpbqq5B6oIgGu0Hy3TLSioZMpTKKv2nrGb2tiqrJDAAi',
    'clinic',
    'active',
    NOW(),
    NOW()
);

INSERT INTO clinics (
    user_id, corp_name, corp_number, representative_name, established_date,
    postal_code, prefecture, address, phone, website_url,
    business_description, facility_count, employee_count, annual_revenue,
    introduction, contact_person_name, contact_person_position,
    contact_person_email, contact_person_phone
) VALUES (
    3,
    '医療法人社団 健康会',
    '1234567890123',
    '佐藤 健一',
    '2010-04-01',
    '160-0022', '東京都', '新宿区新宿3-1-1',
    '03-1234-5678',
    'https://kenkokai-example.jp',
    '内科・循環器内科を中心に、都内で5院のクリニックを運営しています。予防医療から専門治療まで、幅広い医療サービスを提供しています。',
    5, 120, '10億円以上',
    '当法人は「地域の皆様の健康を守る」をモットーに、質の高い医療サービスの提供に努めております。若い医師の育成にも力を入れており、将来の独立開業を支援する制度も整備しております。',
    '田中 美咲',
    '人事部長',
    'tanaka@kenkokai-example.jp',
    '03-1234-5679'
);

-- テスト用求人
INSERT INTO jobs (
    clinic_id, title, facility_name, postal_code, prefecture, address,
    description, work_hours, salary_min, salary_max, salary_description,
    benefits, requirements, recruitment_count, application_deadline,
    transfer_min_tenure_months, transfer_performance_target, transfer_price_type,
    transfer_price_fixed, transfer_scope, transfer_option_deadline,
    transfer_other_conditions, status, published_at
) VALUES (
    1,
    '【内科】渋谷クリニック院長候補募集 ～将来の独立開業を全面サポート～',
    '健康会 渋谷クリニック',
    '150-0002', '東京都', '渋谷区渋谷2-1-1 渋谷メディカルビル3F',
    '当院は渋谷駅から徒歩5分の好立地にある内科クリニックです。主に生活習慣病の治療と予防医療を行っております。\n\n■業務内容\n・外来診療（1日平均30〜40名）\n・健康診断\n・訪問診療（週1回程度）\n・スタッフマネジメント\n\n■クリニックの特徴\n・電子カルテ完備\n・最新の検査機器を導入\n・看護師5名、事務スタッフ3名のチーム体制',
    '【勤務時間】\n平日: 9:00〜18:00（休憩1時間）\n土曜: 9:00〜13:00\n\n【休日】\n日曜・祝日、夏季休暇5日、年末年始6日',
    1800, 2500,
    '年収1,800万円〜2,500万円\n※経験・能力を考慮の上、決定いたします\n※賞与年2回あり',
    '・社会保険完備\n・交通費全額支給\n・学会参加費補助\n・開業支援制度あり',
    '・医師免許をお持ちの方\n・内科経験3年以上\n・将来の独立開業に意欲のある方',
    1,
    '2026-06-30',
    36,
    '年間売上1億円以上の維持',
    'fixed',
    5000,
    '・クリニックの建物（賃貸契約引継）\n・医療機器一式\n・患者データベース\n・スタッフ雇用契約の引継\n・法人からの経営サポート（希望制）',
    '2031-03-31',
    '・譲渡後も法人グループとしての連携が可能\n・開業資金の一部融資制度あり',
    'published',
    NOW()
),
(
    1,
    '【皮膚科】新規開院クリニック院長候補 ～美容皮膚科も展開予定～',
    '健康会 新宿スキンクリニック（仮称）',
    '160-0023', '東京都', '新宿区西新宿1-1-1',
    '2026年秋にオープン予定の皮膚科・美容皮膚科クリニックの院長候補を募集します。\n\n■業務内容\n・一般皮膚科診療\n・美容皮膚科診療\n・クリニックの立ち上げ・運営\n\n■開院準備から携われる貴重なポジションです\n内装設計、機器選定など、一から作り上げる経験ができます。',
    '【勤務時間】\n平日: 10:00〜19:00（休憩1時間）\n土曜: 10:00〜17:00\n\n【休日】\n日曜・祝日・水曜',
    2000, 3000,
    '年収2,000万円〜3,000万円\n※インセンティブ制度あり\n※美容施術売上に応じたボーナスあり',
    '・社会保険完備\n・学会参加費全額補助\n・海外研修制度あり',
    '・皮膚科専門医\n・美容皮膚科経験者優遇\n・クリニック経営に興味のある方',
    1,
    '2026-05-31',
    24,
    NULL,
    'formula',
    NULL,
    '・クリニック設備一式\n・患者基盤\n・スタッフ',
    '2030-12-31',
    '譲渡価格は開院後2年間の平均売上の1.5倍を基準に算定',
    'published',
    NOW()
);

INSERT INTO job_specialties (job_id, specialty_id) VALUES
(1, 1),
(1, 12),
(2, 6),
(2, 15);
