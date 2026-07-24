<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/auth.php';
requireLogin();

$content = getContent();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCsrf($_POST['csrf'] ?? '')) {
    $content['skills_row1'] = array_values(array_filter(array_map('trim', explode(',', $_POST['skills_row1'] ?? ''))));
    $content['skills_row2'] = array_values(array_filter(array_map('trim', explode(',', $_POST['skills_row2'] ?? ''))));
    saveContent($content);
    flash('success', 'Skills updated.');
    redirect('skills.php');
}

$pageTitle = 'Skills';
$pageSubtitle = 'Edit scrolling skills marquee';
require __DIR__ . '/includes/header.php';
?>

<form method="POST" class="form-panel">
    <input type="hidden" name="csrf" value="<?= e(csrfToken()) ?>">
    <div class="form-group"><label>Skills Row 1 (comma separated)</label><textarea name="skills_row1" rows="3"><?= e(implode(', ', $content['skills_row1'] ?? [])) ?></textarea></div>
    <div class="form-group"><label>Skills Row 2 (comma separated)</label><textarea name="skills_row2" rows="3"><?= e(implode(', ', $content['skills_row2'] ?? [])) ?></textarea></div>
    <div class="form-actions"><button class="btn btn-primary" type="submit"><i class='bx bx-save'></i> Save</button></div>
</form>

<?php require __DIR__ . '/includes/footer.php'; ?>
