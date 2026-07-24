<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/functions.php';

trackVisit();
$content = getContent();
$site = $content['site'] ?? [];
$hero = $content['hero'] ?? [];
$about = $content['about'] ?? [];
$contact = $content['contact'] ?? [];
$social = $content['social'] ?? [];
$projects = array_values(array_filter($content['projects'] ?? [], fn($p) => !empty($p['active'])));
$experience = $content['experience'] ?? [];
$education = $content['education'] ?? [];
$marquee = $content['marquee'] ?? [];
$skills1 = $content['skills_row1'] ?? [];
$skills2 = $content['skills_row2'] ?? [];
$roles = $content['roles'] ?? [];
$headlineLines = $hero['headline_lines'] ?? ['Building', 'digital', 'products', 'that matter.'];
$outlineIdx = (int) ($hero['outline_line_index'] ?? 1);
$accentIdx = (int) ($hero['accent_line_index'] ?? 2);
$contactHeading = str_replace("\n", '<br>', e($contact['heading'] ?? "Have a project?\nLet's build it."));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= e($site['description'] ?? '') ?>">
    <title><?= e($site['title'] ?? 'Portfolio') ?></title>
    <link rel="stylesheet" href="style.css">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@500;600;700;800&family=Instrument+Sans:ital,wght@0,400;0,500;0,600;1,400&display=swap" rel="stylesheet">
</head>
<body>

    <div class="noise" aria-hidden="true"></div>
    <div class="cursor-glow" id="cursor-glow" aria-hidden="true"></div>

    <header class="nav">
        <a href="#home" class="nav-brand">NAVEED ALI</a>
        <nav class="nav-links">
            <a href="#about"><span>01</span> About</a>
            <a href="#work"><span>02</span> Work</a>
            <a href="#journey"><span>03</span> Journey</a>
            <a href="#contact"><span>04</span> Contact</a>
        </nav>
        <a href="#contact" class="nav-cta">
            <span>Start a Project</span>
            <i class='bx bx-right-arrow-alt'></i>
        </a>
        <button class="nav-toggle" id="nav-toggle" aria-label="Menu"><span></span><span></span></button>
    </header>

    <section class="hero" id="home">
        <div class="hero-grid">
            <div class="hero-copy">
                <p class="eyebrow"><?= e($hero['eyebrow'] ?? '') ?></p>
                <h1 class="hero-headline">
                    <?php foreach ($headlineLines as $i => $line): ?>
                        <?php
                        $class = 'line';
                        if ($i === $outlineIdx) $class .= ' outline';
                        if ($i === $accentIdx) $class .= ' accent';
                        ?>
                        <span class="<?= $class ?>"><?= e($line) ?></span>
                    <?php endforeach; ?>
                </h1>
                <p class="hero-sub"><?= e($hero['subtitle'] ?? '') ?></p>
                <div class="hero-actions">
                    <a href="#work" class="btn btn-lime">View My Work</a>
                    <a href="<?= e($site['cv_file'] ?? 'Naveed-Ali.pdf') ?>" download class="btn btn-ghost">Download CV</a>
                </div>
            </div>
            <div class="hero-photo">
                <div class="photo-frame">
                    <img src="<?= e($hero['photo'] ?? 'images/2.png') ?>" alt="Naveed Ali">
                </div>
                <div class="photo-tag"><?= e($hero['photo_tag'] ?? '') ?></div>
            </div>
        </div>
        <div class="hero-bottom">
            <div class="hero-role">
                <span><?= e($hero['role_label'] ?? 'Currently focused on') ?></span>
                <strong id="role-text"><?= e($hero['default_role'] ?? '') ?></strong>
            </div>
            <div class="hero-socials">
                <?php if (!empty($social['linkedin'])): ?><a href="<?= e($social['linkedin']) ?>" target="_blank" rel="noopener">LinkedIn</a><?php endif; ?>
                <?php if (!empty($social['facebook'])): ?><a href="<?= e($social['facebook']) ?>" target="_blank" rel="noopener">Facebook</a><?php endif; ?>
                <?php if (!empty($social['whatsapp'])): ?><a href="<?= e($social['whatsapp']) ?>" target="_blank" rel="noopener">WhatsApp</a><?php endif; ?>
            </div>
        </div>
    </section>

    <div class="marquee-wrap" aria-hidden="true">
        <div class="marquee">
            <?php for ($r = 0; $r < 2; $r++): ?>
                <?php foreach ($marquee as $item): ?>
                    <span><?= e($item) ?></span><span>•</span>
                <?php endforeach; ?>
            <?php endfor; ?>
        </div>
    </div>

    <section class="about" id="about">
        <div class="section-top reveal">
            <span class="section-num">01</span>
            <h2><?= e($about['heading'] ?? 'About Me') ?></h2>
        </div>
        <div class="bento reveal">
            <div class="bento-card bento-photo">
                <img src="<?= e($about['photo'] ?? '') ?>" alt="About">
            </div>
            <div class="bento-card bento-bio">
                <h3><?= e($about['title'] ?? '') ?></h3>
                <p><?= e($about['bio'] ?? '') ?></p>
            </div>
            <div class="bento-card bento-stat">
                <strong><?= e($about['stat1_value'] ?? '') ?></strong>
                <span><?= e($about['stat1_label'] ?? '') ?></span>
            </div>
            <div class="bento-card bento-stat">
                <strong><?= e($about['stat2_value'] ?? '') ?></strong>
                <span><?= e($about['stat2_label'] ?? '') ?></span>
            </div>
            <div class="bento-card bento-location">
                <i class='bx bx-map-pin'></i>
                <div>
                    <span><?= e($about['location_label'] ?? 'Based in') ?></span>
                    <strong><?= e($about['location'] ?? '') ?></strong>
                </div>
            </div>
            <div class="bento-card bento-services">
                <span>Services</span>
                <ul>
                    <?php foreach ($about['services'] ?? [] as $service): ?>
                        <li><?= e($service) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </section>

    <section class="work" id="work">
        <div class="section-top reveal">
            <span class="section-num">02</span>
            <h2>Selected Work</h2>
        </div>
        <div class="work-list">
            <?php foreach ($projects as $index => $project): ?>
                <article class="work-item reveal">
                    <a href="<?= e($project['url'] ?? '#') ?>" target="_blank" rel="noopener" class="work-link">
                        <span class="work-num"><?= str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) ?></span>
                        <div class="work-info">
                            <h3><?= e($project['title'] ?? '') ?></h3>
                            <p><?= e($project['description'] ?? '') ?></p>
                        </div>
                        <span class="work-arrow"><i class='bx bx-right-arrow-alt'></i></span>
                        <div class="work-preview">
                            <img src="<?= e($project['image'] ?? '') ?>" alt="<?= e($project['title'] ?? '') ?>">
                        </div>
                    </a>
                </article>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="journey" id="journey">
        <div class="section-top reveal">
            <span class="section-num">03</span>
            <h2>Experience & Education</h2>
        </div>
        <div class="journey-tabs reveal">
            <button class="tab active" data-tab="experience">Experience</button>
            <button class="tab" data-tab="education">Education</button>
        </div>
        <div class="journey-panel active reveal" id="experience">
            <?php foreach ($experience as $item): ?>
                <div class="journey-row<?= !empty($item['current']) ? ' current' : '' ?>">
                    <span class="j-year"><?= e($item['period'] ?? '') ?></span>
                    <div class="j-content">
                        <h4><?= e($item['title'] ?? '') ?></h4>
                        <p><?= e($item['description'] ?? '') ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="journey-panel reveal" id="education">
            <?php foreach ($education as $item): ?>
                <div class="journey-row">
                    <span class="j-year"><?= e($item['period'] ?? '') ?></span>
                    <div class="j-content">
                        <h4><?= e($item['title'] ?? '') ?></h4>
                        <p><?= e($item['description'] ?? '') ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="skills" id="skills">
        <div class="skills-row skills-row-1">
            <?php foreach (array_merge($skills1, $skills1) as $skill): ?>
                <span><?= e($skill) ?></span>
            <?php endforeach; ?>
        </div>
        <div class="skills-row skills-row-2">
            <?php foreach (array_merge($skills2, $skills2) as $skill): ?>
                <span><?= e($skill) ?></span>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="contact" id="contact">
        <div class="contact-inner reveal">
            <div class="contact-left">
                <span class="section-num">04</span>
                <h2><?= $contactHeading ?></h2>
                <p><?= e($contact['subtitle'] ?? '') ?></p>
                <div class="contact-details">
                    <?php if (!empty($contact['email'])): ?>
                        <a href="mailto:<?= e($contact['email']) ?>"><i class='bx bx-envelope'></i> <?= e($contact['email']) ?></a>
                    <?php endif; ?>
                    <?php if (!empty($contact['whatsapp'])): ?>
                        <a href="https://wa.me/<?= e(preg_replace('/\D/', '', $contact['whatsapp'])) ?>" target="_blank" rel="noopener"><i class='bx bxl-whatsapp'></i> <?= e($contact['phone'] ?? $contact['whatsapp']) ?></a>
                    <?php endif; ?>
                    <?php if (!empty($contact['phone'])): ?>
                        <a href="tel:<?= e(preg_replace('/\s/', '', $contact['phone'])) ?>"><i class='bx bx-phone'></i> Call Me</a>
                    <?php endif; ?>
                </div>
            </div>
            <form class="contact-form" action="api/contact.php" method="POST">
                <div class="field"><input type="text" name="name" placeholder="Your Name" required></div>
                <div class="field"><input type="email" name="email" placeholder="Email Address" required></div>
                <div class="field"><input type="text" name="project_type" placeholder="Project Type (Website, App, etc.)"></div>
                <div class="field"><textarea name="message" rows="5" placeholder="Tell me about your project..." required></textarea></div>
                <button type="submit" class="btn btn-lime btn-full">Send Message <i class='bx bx-send'></i></button>
            </form>
        </div>
    </section>

    <footer class="footer">
        <p>&copy; <?= e($site['footer_year'] ?? date('Y')) ?> <?= e($site['footer_name'] ?? 'Naveed Ali') ?></p>
        <a href="#home" class="footer-top">Back to top ↑</a>
    </footer>

    <script>
        window.PORTFOLIO_ROLES = <?= json_encode($roles, JSON_UNESCAPED_UNICODE) ?>;
    </script>
    <script src="script.js"></script>
</body>
</html>
