<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/auth.php';
requireLogin();

$content = getContent();
$type = $_GET['type'] ?? 'experience';
$action = $_GET['action'] ?? 'list';
$id = $_GET['id'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCsrf($_POST['csrf'] ?? '')) {
    $postAction = $_POST['action'] ?? '';
    $section = $_POST['section'] ?? 'experience';
    $key = $section === 'education' ? 'education' : 'experience';
    $items = $content[$key] ?? [];

    if ($postAction === 'save') {
        $item = [
            'id' => $_POST['id'] ?: uniqid(substr($key, 0, 2) . '_'),
            'period' => trim($_POST['period'] ?? ''),
            'title' => trim($_POST['title'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
        ];
        if ($key === 'experience') {
            $item['current'] = isset($_POST['current']);
            if ($item['current']) {
                foreach ($items as &$existing) {
                    $existing['current'] = false;
                }
                unset($existing);
            }
        }

        $found = false;
        foreach ($items as $i => $row) {
            if ($row['id'] === $item['id']) {
                $items[$i] = $item;
                $found = true;
                break;
            }
        }
        if (!$found) {
            array_unshift($items, $item);
        }

        $content[$key] = $items;
        saveContent($content);
        flash('success', ucfirst($key) . ' entry saved.');
        redirect('journey.php?type=' . $key);
    }

    if ($postAction === 'delete') {
        $deleteId = $_POST['id'] ?? '';
        $content[$key] = array_values(array_filter($items, fn($r) => $r['id'] !== $deleteId));
        saveContent($content);
        flash('success', 'Entry deleted.');
        redirect('journey.php?type=' . $key);
    }
}

$key = $type === 'education' ? 'education' : 'experience';
$items = $content[$key] ?? [];
$editItem = null;
if ($action === 'edit' && $id) {
    foreach ($items as $row) {
        if ($row['id'] === $id) {
            $editItem = $row;
            break;
        }
    }
}

$pageTitle = 'Experience & Education';
$pageSubtitle = 'Manage your career timeline';
require __DIR__ . '/includes/header.php';
?>

<div class="form-actions" style="margin-bottom:1rem">
    <a href="journey.php?type=experience" class="btn btn-sm <?= $key === 'experience' ? 'btn-primary' : 'btn-secondary' ?>">Experience</a>
    <a href="journey.php?type=education" class="btn btn-sm <?= $key === 'education' ? 'btn-primary' : 'btn-secondary' ?>">Education</a>
</div>

<div class="form-panel">
    <h3><?= $editItem ? 'Edit Entry' : 'Add ' . ucfirst($key) ?></h3>
    <form method="POST">
        <input type="hidden" name="csrf" value="<?= e(csrfToken()) ?>">
        <input type="hidden" name="action" value="save">
        <input type="hidden" name="section" value="<?= e($key) ?>">
        <input type="hidden" name="id" value="<?= e($editItem['id'] ?? '') ?>">
        <div class="form-grid">
            <div class="form-group"><label>Period</label><input type="text" name="period" value="<?= e($editItem['period'] ?? '') ?>" placeholder="Feb 2026 — Present"></div>
            <?php if ($key === 'experience'): ?>
                <div class="form-group checkbox-row"><label><input type="checkbox" name="current" <?= !empty($editItem['current']) ? 'checked' : '' ?>> Current Job</label></div>
            <?php endif; ?>
            <div class="form-group full"><label>Title</label><input type="text" name="title" value="<?= e($editItem['title'] ?? '') ?>"></div>
            <div class="form-group full"><label>Description</label><textarea name="description" rows="4"><?= e($editItem['description'] ?? '') ?></textarea></div>
        </div>
        <div class="form-actions"><button class="btn btn-primary" type="submit"><i class='bx bx-save'></i> Save</button></div>
    </form>
</div>

<div class="panel">
    <div class="table-wrap">
        <table class="data-table">
            <thead><tr><th>Period</th><th>Title</th><th>Actions</th></tr></thead>
            <tbody>
                <?php foreach ($items as $row): ?>
                    <tr>
                        <td><?= e($row['period'] ?? '') ?><?= !empty($row['current']) ? ' <span class="badge badge-orange">Current</span>' : '' ?></td>
                        <td><?= e($row['title'] ?? '') ?></td>
                        <td>
                            <a class="btn btn-sm btn-secondary" href="journey.php?type=<?= e($key) ?>&action=edit&id=<?= e($row['id'] ?? '') ?>">Edit</a>
                            <form method="POST" style="display:inline" onsubmit="return confirm('Delete?')">
                                <input type="hidden" name="csrf" value="<?= e(csrfToken()) ?>">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="section" value="<?= e($key) ?>">
                                <input type="hidden" name="id" value="<?= e($row['id'] ?? '') ?>">
                                <button class="btn btn-sm btn-danger" type="submit">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
