<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/auth.php';
requireLogin();

$content = getContent();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCsrf($_POST['csrf'] ?? '')) {
    $content['contact'] = [
        'heading' => trim($_POST['heading'] ?? ''),
        'subtitle' => trim($_POST['subtitle'] ?? ''),
        'email' => trim($_POST['email'] ?? ''),
        'phone' => trim($_POST['phone'] ?? ''),
        'whatsapp' => trim($_POST['whatsapp'] ?? ''),
    ];
    $content['social'] = [
        'linkedin' => trim($_POST['linkedin'] ?? ''),
        'facebook' => trim($_POST['facebook'] ?? ''),
        'whatsapp' => trim($_POST['whatsapp_url'] ?? ''),
    ];
    saveContent($content);
    flash('success', 'Contact settings updated.');
    redirect('contact-settings.php');
}

$contact = $content['contact'] ?? [];
$social = $content['social'] ?? [];
$pageTitle = 'Contact Settings';
$pageSubtitle = 'Email, phone and social links';
require __DIR__ . '/includes/header.php';
?>

<form method="POST" class="form-panel">
    <input type="hidden" name="csrf" value="<?= e(csrfToken()) ?>">
    <h3>Contact Section</h3>
    <div class="form-grid">
        <div class="form-group full"><label>Heading (use \n for line break)</label><textarea name="heading" rows="2"><?= e($contact['heading'] ?? '') ?></textarea></div>
        <div class="form-group full"><label>Subtitle</label><input type="text" name="subtitle" value="<?= e($contact['subtitle'] ?? '') ?>"></div>
        <div class="form-group"><label>Email</label><input type="email" name="email" value="<?= e($contact['email'] ?? '') ?>"></div>
        <div class="form-group"><label>Phone Display</label><input type="text" name="phone" value="<?= e($contact['phone'] ?? '') ?>"></div>
        <div class="form-group"><label>WhatsApp Number</label><input type="text" name="whatsapp" value="<?= e($contact['whatsapp'] ?? '') ?>"></div>
    </div>
    <h3 style="margin-top:2rem">Social Links</h3>
    <div class="form-grid">
        <div class="form-group"><label>LinkedIn URL</label><input type="url" name="linkedin" value="<?= e($social['linkedin'] ?? '') ?>"></div>
        <div class="form-group"><label>Facebook URL</label><input type="url" name="facebook" value="<?= e($social['facebook'] ?? '') ?>"></div>
        <div class="form-group full"><label>WhatsApp Link</label><input type="url" name="whatsapp_url" value="<?= e($social['whatsapp'] ?? '') ?>"></div>
    </div>
    <div class="form-actions"><button class="btn btn-primary" type="submit"><i class='bx bx-save'></i> Save</button></div>
</form>

<?php require __DIR__ . '/includes/footer.php'; ?>
