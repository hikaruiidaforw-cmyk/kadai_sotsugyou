<?php
/**
 * ログアウト処理
 */

Auth::logout();
header('Location: ' . BASE_PATH . '/?page=login');
exit;
