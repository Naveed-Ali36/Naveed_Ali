<?php
declare(strict_types=1);

define('ROOT_PATH', dirname(__DIR__));
define('DATA_PATH', ROOT_PATH . '/data');
define('CONTENT_FILE', DATA_PATH . '/content.json');
define('MESSAGES_FILE', DATA_PATH . '/messages.json');
define('ANALYTICS_FILE', DATA_PATH . '/analytics.json');
define('SETTINGS_FILE', DATA_PATH . '/settings.json');

define('ADMIN_SESSION_KEY', 'portfolio_admin');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
