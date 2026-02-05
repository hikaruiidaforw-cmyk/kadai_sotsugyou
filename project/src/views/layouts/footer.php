    </main>

    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div>
                    <div class="footer-brand">Dr. Option</div>
                    <p class="footer-description">
                        将来譲渡特約付き・クリニック院長就職支援サービス。<br>
                        雇われ院長から始める、確実な独立への道。
                    </p>
                </div>

                <div>
                    <h4 class="footer-title">医師の方へ</h4>
                    <ul class="footer-links">
                        <li><a href="<?php echo BASE_PATH; ?>/?page=register/doctor">会員登録</a></li>
                        <li><a href="<?php echo BASE_PATH; ?>/?page=doctor/jobs">求人を探す</a></li>
                        <li><a href="#">よくある質問</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="footer-title">法人の方へ</h4>
                    <ul class="footer-links">
                        <li><a href="<?php echo BASE_PATH; ?>/?page=register/clinic">法人登録</a></li>
                        <li><a href="#">求人掲載について</a></li>
                        <li><a href="#">料金プラン</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="footer-title">サポート</h4>
                    <ul class="footer-links">
                        <li><a href="<?php echo BASE_PATH; ?>/?page=terms">利用規約</a></li>
                        <li><a href="<?php echo BASE_PATH; ?>/?page=privacy">プライバシーポリシー</a></li>
                        <li><a href="#">お問い合わせ</a></li>
                    </ul>
                </div>
            </div>

            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> Dr. Option. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="<?php echo BASE_PATH; ?>/js/common.js"></script>
    <?php if (isset($extraJs)): ?>
        <?php foreach ($extraJs as $js): ?>
            <script src="<?php echo e($js); ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>
