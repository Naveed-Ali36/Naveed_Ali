<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/auth.php';
requireLogin();

$content = getContent();
$settings = getSettings();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCsrf($_POST['csrf'] ?? '')) {
    $tab = $_POST['tab'] ?? 'site';

    if ($tab === 'site') {
        $content['site'] = [
            'title' => trim($_POST['title'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'cv_file' => trim($_POST['cv_file'] ?? ''),
            'footer_year' => trim($_POST['footer_year'] ?? date('Y')),
            'footer_name' => trim($_POST['footer_name'] ?? ''),
        ];
        saveContent($content);
        flash('success', 'Site settings saved.');
    }

    if ($tab === 'password') {
        $newUser = trim($_POST['admin_user'] ?? '');
        $newPass = $_POST['new_password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        if ($newUser === '') {
            flash('error', 'Username cannot be empty.');
        } elseif ($newPass !== '' && $newPass !== $confirm) {
            flash('error', 'Passwords do not match.');
        } else {
            $settings['admin_user'] = $newUser;
            if ($newPass !== '') {
                $settings['admin_pass'] = password_hash($newPass, PASSWORD_DEFAULT);
            }
            updateSettings($settings);
            $_SESSION[ADMIN_SESSION_KEY] = $newUser;
            flash('success', 'Account settings updated.');
        }
    }

    redirect('settings.php');
}

$site = $content['site'] ?? [];
$pageTitle = 'Settings';
$pageSubtitle = 'Site info and admin account';
require __DIR__ . '/includes/header.php';
?>

<form method="POST" class="form-panel">
    <input type="hidden" name="csrf" value="<?= e(csrfToken()) ?>">
    <input type="hidden" name="tab" value="site">
    <h3>Site Settings</h3>
    <div class="form-grid">
        <div class="form-group"><label>Page Title</label><input type="text" name="title" value="<?= e($site['title'] ?? '') ?>"></div>
        <div class="form-group"><label>CV File Path</label><input type="text" name="cv_file" value="<?= e($site['cv_file'] ?? '') ?>"></div>
        <div class="form-group full"><label>Meta Description</label><textarea name="description" rows="2"><?= e($site['description'] ?? '') ?></textarea></div>
        <div class="form-group"><label>Footer Year</label><input type="text" name="footer_year" value="<?= e($site['footer_year'] ?? '') ?>"></div>
        <div class="form-group"><label>Footer Name</label><input type="text" name="footer_name" value="<?= e($site['footer_name'] ?? '') ?>"></div>
    </div>
    <div class="form-actions"><button class="btn btn-primary" type="submit"><i class='bx bx-save'></i> Save Site</button></div>
</form>

<form method="POST" class="form-panel">
    <input type="hidden" name="csrf" value="<?= e(csrfToken()) ?>">
    <input type="hidden" name="tab" value="password">
    <h3>Admin Account</h3>
    <div class="form-grid">
        <div class="form-group"><label>Username</label><input type="text" name="admin_user" value="<?= e($settings['admin_user'] ?? 'admin') ?>" required></div>
        <div class="form-group"><label>New Password</label><input type="password" name="new_password" placeholder="Leave blank to keep current"></div>
        <div class="form-group"><label>Confirm Password</label><input type="password" name="confirm_password"></div>
    </div>
    <div class="form-actions"><button class="btn btn-primary" type="submit"><i class='bx bx-shield'></i> Update Account</button></div>
</form>

<?php require __DIR__ . '/includes/footer.php'; ?>
