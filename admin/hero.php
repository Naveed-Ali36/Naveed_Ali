<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/auth.php';
requireLogin();

$content = getContent();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCsrf($_POST['csrf'] ?? '')) {
    $content['hero'] = [
        'eyebrow' => trim($_POST['eyebrow'] ?? ''),
        'headline_lines' => array_values(array_filter(array_map('trim', explode("\n", $_POST['headline_lines'] ?? '')))),
        'outline_line_index' => (int) ($_POST['outline_line_index'] ?? 1),
        'accent_line_index' => (int) ($_POST['accent_line_index'] ?? 2),
        'subtitle' => trim($_POST['subtitle'] ?? ''),
        'photo' => trim($_POST['photo'] ?? ''),
        'photo_tag' => trim($_POST['photo_tag'] ?? ''),
        'role_label' => trim($_POST['role_label'] ?? ''),
        'default_role' => trim($_POST['default_role'] ?? ''),
    ];
    $content['roles'] = array_values(array_filter(array_map('trim', explode("\n", $_POST['roles'] ?? ''))));
    $content['marquee'] = array_values(array_filter(array_map('trim', explode(',', $_POST['marquee'] ?? ''))));
    saveContent($content);
    flash('success', 'Hero section updated successfully.');
    redirect('hero.php');
}

$hero = $content['hero'] ?? [];
$pageTitle = 'Hero Section';
$pageSubtitle = 'Edit homepage headline, photo and rotating roles';
require __DIR__ . '/includes/header.php';
?>

<form method="POST" class="form-panel">
    <input type="hidden" name="csrf" value="<?= e(csrfToken()) ?>">
    <h3>Hero Content</h3>
    <div class="form-grid">
        <div class="form-group full"><label>Eyebrow Text</label><input type="text" name="eyebrow" value="<?= e($hero['eyebrow'] ?? '') ?>"></div>
        <div class="form-group full"><label>Headline Lines (one per line)</label><textarea name="headline_lines" rows="4"><?= e(implode("\n", $hero['headline_lines'] ?? [])) ?></textarea></div>
        <div class="form-group"><label>Outline Line # (0-based)</label><input type="number" name="outline_line_index" value="<?= e((string) ($hero['outline_line_index'] ?? 1)) ?>"></div>
        <div class="form-group"><label>Accent Line # (0-based)</label><input type="number" name="accent_line_index" value="<?= e((string) ($hero['accent_line_index'] ?? 2)) ?>"></div>
        <div class="form-group full"><label>Subtitle</label><textarea name="subtitle" rows="3"><?= e($hero['subtitle'] ?? '') ?></textarea></div>
        <div class="form-group"><label>Photo Path</label><input type="text" name="photo" value="<?= e($hero['photo'] ?? '') ?>"></div>
        <div class="form-group"><label>Photo Tag</label><input type="text" name="photo_tag" value="<?= e($hero['photo_tag'] ?? '') ?>"></div>
        <div class="form-group"><label>Role Label</label><input type="text" name="role_label" value="<?= e($hero['role_label'] ?? '') ?>"></div>
        <div class="form-group"><label>Default Role</label><input type="text" name="default_role" value="<?= e($hero['default_role'] ?? '') ?>"></div>
        <div class="form-group full"><label>Rotating Roles (one per line)</label><textarea name="roles" rows="5"><?= e(implode("\n", $content['roles'] ?? [])) ?></textarea></div>
        <div class="form-group full"><label>Marquee Skills (comma separated)</label><input type="text" name="marquee" value="<?= e(implode(', ', $content['marquee'] ?? [])) ?>"></div>
    </div>
    <div class="form-actions"><button class="btn btn-primary" type="submit"><i class='bx bx-save'></i> Save Changes</button></div>
</form>

<?php require __DIR__ . '/includes/footer.php'; ?>
