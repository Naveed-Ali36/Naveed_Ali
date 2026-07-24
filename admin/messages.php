<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/auth.php';
requireLogin();

$messages = getMessages();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCsrf($_POST['csrf'] ?? '')) {
    $action = $_POST['action'] ?? '';

    if ($action === 'mark_read') {
        $id = $_POST['id'] ?? '';
        foreach ($messages as &$msg) {
            if (($msg['id'] ?? '') === $id) {
                $msg['read'] = true;
            }
        }
        unset($msg);
        updateMessages($messages);
        flash('success', 'Message marked as read.');
    }

    if ($action === 'delete') {
        $id = $_POST['id'] ?? '';
        $messages = array_values(array_filter($messages, fn($m) => ($m['id'] ?? '') !== $id));
        updateMessages($messages);
        flash('success', 'Message deleted.');
    }

    if ($action === 'mark_all_read') {
        foreach ($messages as &$msg) {
            $msg['read'] = true;
        }
        unset($msg);
        updateMessages($messages);
        flash('success', 'All messages marked as read.');
    }

    redirect('messages.php');
}

$pageTitle = 'Messages';
$pageSubtitle = 'Contact form submissions from your website';
require __DIR__ . '/includes/header.php';
?>

<div class="form-actions" style="margin-bottom:1rem">
    <form method="POST" style="display:inline">
        <input type="hidden" name="csrf" value="<?= e(csrfToken()) ?>">
        <input type="hidden" name="action" value="mark_all_read">
        <button class="btn btn-secondary" type="submit">Mark All Read</button>
    </form>
</div>

<div class="panel">
    <?php if (empty($messages)): ?>
        <p class="empty-state">No messages yet.</p>
    <?php else: ?>
        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr><th>Date</th><th>Name</th><th>Email</th><th>Project</th><th>Message</th><th>Status</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($messages as $msg): ?>
                        <tr>
                            <td><?= e($msg['created_at'] ?? '') ?></td>
                            <td><?= e($msg['name'] ?? '') ?></td>
                            <td><a href="mailto:<?= e($msg['email'] ?? '') ?>"><?= e($msg['email'] ?? '') ?></a></td>
                            <td><?= e($msg['project_type'] ?? '-') ?></td>
                            <td><?= e($msg['message'] ?? '') ?></td>
                            <td><?= !empty($msg['read']) ? '<span class="badge badge-green">Read</span>' : '<span class="badge badge-orange">New</span>' ?></td>
                            <td>
                                <?php if (empty($msg['read'])): ?>
                                    <form method="POST" style="display:inline">
                                        <input type="hidden" name="csrf" value="<?= e(csrfToken()) ?>">
                                        <input type="hidden" name="action" value="mark_read">
                                        <input type="hidden" name="id" value="<?= e($msg['id'] ?? '') ?>">
                                        <button class="btn btn-sm btn-secondary" type="submit">Read</button>
                                    </form>
                                <?php endif; ?>
                                <form method="POST" style="display:inline" onsubmit="return confirm('Delete message?')">
                                    <input type="hidden" name="csrf" value="<?= e(csrfToken()) ?>">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= e($msg['id'] ?? '') ?>">
                                    <button class="btn btn-sm btn-danger" type="submit">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
