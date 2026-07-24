<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/auth.php';
requireLogin();

$content = getContent();
$projects = $content['projects'] ?? [];
$action = $_GET['action'] ?? 'list';
$id = $_GET['id'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCsrf($_POST['csrf'] ?? '')) {
    $postAction = $_POST['action'] ?? '';

    if ($postAction === 'save') {
        $project = [
            'id' => $_POST['id'] ?: uniqid('p_'),
            'title' => trim($_POST['title'] ?? ''),
            'url' => trim($_POST['url'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'image' => trim($_POST['image'] ?? ''),
            'active' => isset($_POST['active']),
        ];

        $found = false;
        foreach ($projects as $i => $p) {
            if ($p['id'] === $project['id']) {
                $projects[$i] = $project;
                $found = true;
                break;
            }
        }
        if (!$found) {
            array_unshift($projects, $project);
        }

        $content['projects'] = $projects;
        saveContent($content);
        flash('success', 'Project saved.');
        redirect('projects.php');
    }

    if ($postAction === 'delete') {
        $deleteId = $_POST['id'] ?? '';
        $content['projects'] = array_values(array_filter($projects, fn($p) => $p['id'] !== $deleteId));
        saveContent($content);
        flash('success', 'Project deleted.');
        redirect('projects.php');
    }
}

$editProject = null;
if ($action === 'edit' && $id) {
    foreach ($projects as $p) {
        if ($p['id'] === $id) {
            $editProject = $p;
            break;
        }
    }
}

$pageTitle = 'Projects';
$pageSubtitle = 'Manage portfolio projects';
require __DIR__ . '/includes/header.php';
?>

<div class="form-panel">
    <h3><?= $editProject ? 'Edit Project' : 'Add New Project' ?></h3>
    <form method="POST">
        <input type="hidden" name="csrf" value="<?= e(csrfToken()) ?>">
        <input type="hidden" name="action" value="save">
        <input type="hidden" name="id" value="<?= e($editProject['id'] ?? '') ?>">
        <div class="form-grid">
            <div class="form-group"><label>Title</label><input type="text" name="title" required value="<?= e($editProject['title'] ?? '') ?>"></div>
            <div class="form-group"><label>URL</label><input type="url" name="url" required value="<?= e($editProject['url'] ?? '') ?>"></div>
            <div class="form-group full"><label>Description</label><input type="text" name="description" value="<?= e($editProject['description'] ?? '') ?>"></div>
            <div class="form-group"><label>Image Path</label><input type="text" name="image" value="<?= e($editProject['image'] ?? '') ?>"></div>
            <div class="form-group checkbox-row"><label><input type="checkbox" name="active" <?= empty($editProject) || !empty($editProject['active']) ? 'checked' : '' ?>> Active on website</label></div>
        </div>
        <div class="form-actions">
            <button class="btn btn-primary" type="submit"><i class='bx bx-save'></i> Save Project</button>
            <?php if ($editProject): ?><a href="projects.php" class="btn btn-secondary">Cancel</a><?php endif; ?>
        </div>
    </form>
</div>

<div class="panel">
    <div class="panel-head"><h3>All Projects</h3></div>
    <div class="table-wrap">
        <table class="data-table">
            <thead><tr><th>Title</th><th>URL</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
                <?php foreach ($projects as $p): ?>
                    <tr>
                        <td><?= e($p['title'] ?? '') ?></td>
                        <td><a href="<?= e($p['url'] ?? '#') ?>" target="_blank"><?= e($p['url'] ?? '') ?></a></td>
                        <td><?= !empty($p['active']) ? '<span class="badge badge-green">Active</span>' : '<span class="badge badge-gray">Hidden</span>' ?></td>
                        <td>
                            <a class="btn btn-sm btn-secondary" href="projects.php?action=edit&id=<?= e($p['id'] ?? '') ?>">Edit</a>
                            <form method="POST" style="display:inline" onsubmit="return confirm('Delete this project?')">
                                <input type="hidden" name="csrf" value="<?= e(csrfToken()) ?>">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= e($p['id'] ?? '') ?>">
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
