<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/auth.php';
requireLogin();

$content = getContent();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCsrf($_POST['csrf'] ?? '')) {
    $content['about'] = [
        'heading' => trim($_POST['heading'] ?? ''),
        'photo' => trim($_POST['photo'] ?? ''),
        'title' => trim($_POST['title'] ?? ''),
        'bio' => trim($_POST['bio'] ?? ''),
        'stat1_value' => trim($_POST['stat1_value'] ?? ''),
        'stat1_label' => trim($_POST['stat1_label'] ?? ''),
        'stat2_value' => trim($_POST['stat2_value'] ?? ''),
        'stat2_label' => trim($_POST['stat2_label'] ?? ''),
        'location_label' => trim($_POST['location_label'] ?? ''),
        'location' => trim($_POST['location'] ?? ''),
        'services' => array_values(array_filter(array_map('trim', explode("\n", $_POST['services'] ?? '')))),
    ];
    saveContent($content);
    flash('success', 'About section updated.');
    redirect('about.php');
}

$about = $content['about'] ?? [];
$pageTitle = 'About Section';
$pageSubtitle = 'Bio, stats, location and services';
require __DIR__ . '/includes/header.php';
?>

<form method="POST" class="form-panel">
    <input type="hidden" name="csrf" value="<?= e(csrfToken()) ?>">
    <div class="form-grid">
        <div class="form-group"><label>Section Heading</label><input type="text" name="heading" value="<?= e($about['heading'] ?? '') ?>"></div>
        <div class="form-group"><label>Photo Path</label><input type="text" name="photo" value="<?= e($about['photo'] ?? '') ?>"></div>
        <div class="form-group full"><label>Title</label><input type="text" name="title" value="<?= e($about['title'] ?? '') ?>"></div>
        <div class="form-group full"><label>Bio</label><textarea name="bio" rows="5"><?= e($about['bio'] ?? '') ?></textarea></div>
        <div class="form-group"><label>Stat 1 Value</label><input type="text" name="stat1_value" value="<?= e($about['stat1_value'] ?? '') ?>"></div>
        <div class="form-group"><label>Stat 1 Label</label><input type="text" name="stat1_label" value="<?= e($about['stat1_label'] ?? '') ?>"></div>
        <div class="form-group"><label>Stat 2 Value</label><input type="text" name="stat2_value" value="<?= e($about['stat2_value'] ?? '') ?>"></div>
        <div class="form-group"><label>Stat 2 Label</label><input type="text" name="stat2_label" value="<?= e($about['stat2_label'] ?? '') ?>"></div>
        <div class="form-group"><label>Location Label</label><input type="text" name="location_label" value="<?= e($about['location_label'] ?? '') ?>"></div>
        <div class="form-group"><label>Location</label><input type="text" name="location" value="<?= e($about['location'] ?? '') ?>"></div>
        <div class="form-group full"><label>Services (one per line)</label><textarea name="services" rows="6"><?= e(implode("\n", $about['services'] ?? [])) ?></textarea></div>
    </div>
    <div class="form-actions"><button class="btn btn-primary" type="submit"><i class='bx bx-save'></i> Save</button></div>
</form>

<?php require __DIR__ . '/includes/footer.php'; ?>
