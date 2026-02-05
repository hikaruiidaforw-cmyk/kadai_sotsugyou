/**
 * MedCareer Bridge - 共通JavaScript
 */

document.addEventListener('DOMContentLoaded', function() {
    // ヘッダーのスクロール処理
    initHeaderScroll();

    // フォームバリデーション
    initFormValidation();

    // アラートの自動非表示
    initAlertAutoHide();

    // スムーズスクロール
    initSmoothScroll();
});

/**
 * ヘッダーのスクロール処理
 */
function initHeaderScroll() {
    const header = document.getElementById('header');
    if (!header) return;

    let lastScroll = 0;

    window.addEventListener('scroll', function() {
        const currentScroll = window.pageYOffset;

        if (currentScroll > 50) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }

        lastScroll = currentScroll;
    });
}

/**
 * フォームバリデーション
 */
function initFormValidation() {
    const forms = document.querySelectorAll('form');

    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;

            requiredFields.forEach(field => {
                const value = field.value.trim();
                const formGroup = field.closest('.form-group');

                if (!value) {
                    isValid = false;
                    field.classList.add('is-invalid');
                    if (formGroup && !formGroup.querySelector('.form-error')) {
                        const error = document.createElement('p');
                        error.className = 'form-error';
                        error.textContent = 'この項目は必須です';
                        formGroup.appendChild(error);
                    }
                } else {
                    field.classList.remove('is-invalid');
                    if (formGroup) {
                        const error = formGroup.querySelector('.form-error');
                        if (error) error.remove();
                    }
                }
            });

            if (!isValid) {
                e.preventDefault();
            }
        });
    });

    // リアルタイムバリデーション
    const inputs = document.querySelectorAll('.form-input, .form-select, .form-textarea');
    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            validateField(this);
        });

        input.addEventListener('input', function() {
            this.classList.remove('is-invalid');
            const formGroup = this.closest('.form-group');
            if (formGroup) {
                const error = formGroup.querySelector('.form-error');
                if (error) error.remove();
            }
        });
    });
}

/**
 * フィールドのバリデーション
 */
function validateField(field) {
    const value = field.value.trim();
    const formGroup = field.closest('.form-group');

    // メールアドレス
    if (field.type === 'email' && value) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(value)) {
            showFieldError(field, formGroup, 'メールアドレスの形式が正しくありません');
            return false;
        }
    }

    // 電話番号
    if (field.type === 'tel' && value) {
        const phoneRegex = /^0\d{9,10}$/;
        const cleanPhone = value.replace(/[-\s]/g, '');
        if (!phoneRegex.test(cleanPhone)) {
            showFieldError(field, formGroup, '電話番号の形式が正しくありません');
            return false;
        }
    }

    // パスワード
    if (field.id === 'password' && value) {
        const passwordRegex = /^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d@$!%*#?&]{8,}$/;
        if (!passwordRegex.test(value)) {
            showFieldError(field, formGroup, 'パスワードは8文字以上で、英字と数字を含む必要があります');
            return false;
        }
    }

    // パスワード確認
    if (field.id === 'password_confirm' && value) {
        const password = document.getElementById('password');
        if (password && password.value !== value) {
            showFieldError(field, formGroup, 'パスワードが一致しません');
            return false;
        }
    }

    return true;
}

/**
 * フィールドエラー表示
 */
function showFieldError(field, formGroup, message) {
    field.classList.add('is-invalid');
    if (formGroup && !formGroup.querySelector('.form-error')) {
        const error = document.createElement('p');
        error.className = 'form-error';
        error.textContent = message;
        formGroup.appendChild(error);
    }
}

/**
 * アラートの自動非表示
 */
function initAlertAutoHide() {
    const alerts = document.querySelectorAll('.alert-success');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.5s ease';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        }, 5000);
    });
}

/**
 * スムーズスクロール
 */
function initSmoothScroll() {
    const links = document.querySelectorAll('a[href^="#"]');
    links.forEach(link => {
        link.addEventListener('click', function(e) {
            const targetId = this.getAttribute('href');
            if (targetId === '#') return;

            const target = document.querySelector(targetId);
            if (target) {
                e.preventDefault();
                const headerHeight = document.querySelector('.header')?.offsetHeight || 0;
                const targetPosition = target.getBoundingClientRect().top + window.pageYOffset - headerHeight - 20;

                window.scrollTo({
                    top: targetPosition,
                    behavior: 'smooth'
                });
            }
        });
    });
}

/**
 * 確認ダイアログ
 */
function confirmAction(message) {
    return confirm(message);
}

/**
 * 数値フォーマット
 */
function formatNumber(num) {
    return new Intl.NumberFormat('ja-JP').format(num);
}

/**
 * 日付フォーマット
 */
function formatDate(dateStr, format = 'YYYY/MM/DD') {
    const date = new Date(dateStr);
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');

    return format
        .replace('YYYY', year)
        .replace('MM', month)
        .replace('DD', day);
}
