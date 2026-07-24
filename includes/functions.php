<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';

function ensureDataDir(): void
{
    if (!is_dir(DATA_PATH)) {
        mkdir(DATA_PATH, 0755, true);
    }
}

function readJson(string $file, array $default = []): array
{
    ensureDataDir();
    if (!file_exists($file)) {
        writeJson($file, $default);
        return $default;
    }

    $raw = file_get_contents($file);
    $data = json_decode($raw ?: '[]', true);
    return is_array($data) ? $data : $default;
}

function writeJson(string $file, array $data): bool
{
    ensureDataDir();
    return (bool) file_put_contents(
        $file,
        json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        LOCK_EX
    );
}

function getSettings(): array
{
    $settings = readJson(SETTINGS_FILE, []);

    if (empty($settings['admin_user']) || empty($settings['admin_pass'])) {
        $settings = [
            'admin_user' => 'admin',
            'admin_pass' => password_hash('Admin@123', PASSWORD_DEFAULT),
        ];
        writeJson(SETTINGS_FILE, $settings);
    }

    return $settings;
}

function updateSettings(array $settings): bool
{
    return writeJson(SETTINGS_FILE, $settings);
}

function getContent(): array
{
    return readJson(CONTENT_FILE, defaultContent());
}

function saveContent(array $content): bool
{
    return writeJson(CONTENT_FILE, $content);
}

function defaultContent(): array
{
    return json_decode(file_get_contents(ROOT_PATH . '/data/content.default.json'), true) ?: [];
}

function getMessages(): array
{
    return readJson(MESSAGES_FILE, []);
}

function saveMessage(array $message): bool
{
    $messages = getMessages();
    $message['id'] = uniqid('msg_', true);
    $message['created_at'] = date('Y-m-d H:i:s');
    $message['read'] = false;
    array_unshift($messages, $message);
    return writeJson(MESSAGES_FILE, $messages);
}

function updateMessages(array $messages): bool
{
    return writeJson(MESSAGES_FILE, $messages);
}

function trackVisit(): void
{
    $analytics = readJson(ANALYTICS_FILE, ['visits' => [], 'total' => 0]);
    $today = date('Y-m-d');

    if (!isset($analytics['visits'][$today])) {
        $analytics['visits'][$today] = 0;
    }

    $analytics['visits'][$today]++;
    $analytics['total'] = ($analytics['total'] ?? 0) + 1;

    if (count($analytics['visits']) > 60) {
        $analytics['visits'] = array_slice($analytics['visits'], -60, null, true);
    }

    writeJson(ANALYTICS_FILE, $analytics);
}

function getAnalytics(): array
{
    return readJson(ANALYTICS_FILE, ['visits' => [], 'total' => 0]);
}

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function flash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash(): ?array
{
    if (!isset($_SESSION['flash'])) {
        return null;
    }
    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return $flash;
}

function redirect(string $url): never
{
    header('Location: ' . $url);
    exit;
}

function csrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCsrf(?string $token): bool
{
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], (string) $token);
}

function adminStats(): array
{
    $content = getContent();
    $messages = getMessages();
    $analytics = getAnalytics();
    $unread = count(array_filter($messages, fn($m) => empty($m['read'])));

    return [
        'projects' => count($content['projects'] ?? []),
        'experience' => count($content['experience'] ?? []),
        'education' => count($content['education'] ?? []),
        'messages' => count($messages),
        'unread_messages' => $unread,
        'total_visits' => $analytics['total'] ?? 0,
        'today_visits' => $analytics['visits'][date('Y-m-d')] ?? 0,
        'visits_chart' => $analytics['visits'] ?? [],
    ];
}

// Initialize content from default on first run
ensureDataDir();
if (!file_exists(CONTENT_FILE) && file_exists(ROOT_PATH . '/data/content.default.json')) {
    copy(ROOT_PATH . '/data/content.default.json', CONTENT_FILE);
}
