<?php
declare(strict_types=1);

header('Content-Type: application/json');

require_once dirname(__DIR__) . '/includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$projectType = trim($_POST['project_type'] ?? '');
$message = trim($_POST['message'] ?? '');

if ($name === '' || $email === '' || $message === '') {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Please fill all required fields.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Invalid email address.']);
    exit;
}

$ok = saveMessage([
    'name' => $name,
    'email' => $email,
    'project_type' => $projectType,
    'message' => $message,
]);

echo json_encode([
    'success' => $ok,
    'message' => $ok ? 'Message sent successfully!' : 'Could not save message.',
]);
