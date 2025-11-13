<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/functions.php';

logout_admin();

header('Location: ' . site_url('admin/login.php'));
exit;

