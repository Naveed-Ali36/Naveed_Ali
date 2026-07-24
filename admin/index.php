<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/auth.php';
requireLogin();

$pageTitle = 'Dashboard';
$pageSubtitle = 'Overview of your portfolio performance';
$loadCharts = true;
$stats = adminStats();
$messages = getMessages();

$visits = $stats['visits_chart'];
$last14 = [];
for ($i = 13; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-{$i} days"));
    $last14[$date] = $visits[$date] ?? 0;
}

$recentMessages = array_slice($messages, 0, 5);

$chartLabels = json_encode(array_keys($last14));
$chartVisits = json_encode(array_values($last14));
$chartMessages = json_encode([
    'read' => count(array_filter($messages, fn($m) => !empty($m['read']))),
    'unread' => $stats['unread_messages'],
]);

require __DIR__ . '/includes/header.php';
?>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon blue"><i class='bx bx-show'></i></div>
        <div>
            <strong><?= (int) $stats['total_visits'] ?></strong>
            <span>Total Visits</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green"><i class='bx bx-trending-up'></i></div>
        <div>
            <strong><?= (int) $stats['today_visits'] ?></strong>
            <span>Today's Visits</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon purple"><i class='bx bx-briefcase'></i></div>
        <div>
            <strong><?= (int) $stats['projects'] ?></strong>
            <span>Active Projects</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon orange"><i class='bx bx-envelope'></i></div>
        <div>
            <strong><?= (int) $stats['messages'] ?></strong>
            <span>Total Messages <?= $stats['unread_messages'] ? '(' . $stats['unread_messages'] . ' new)' : '' ?></span>
        </div>
    </div>
</div>

<div class="charts-grid">
    <div class="panel chart-panel">
        <div class="panel-head">
            <h3>Visitor Analytics</h3>
            <span>Last 14 days</span>
        </div>
        <canvas id="visitsChart" height="120"></canvas>
    </div>
    <div class="panel chart-panel">
        <div class="panel-head">
            <h3>Messages Overview</h3>
            <span>Read vs Unread</span>
        </div>
        <canvas id="messagesChart" height="120"></canvas>
    </div>
</div>

<div class="charts-grid">
    <div class="panel">
        <div class="panel-head">
            <h3>Content Summary</h3>
        </div>
        <div class="summary-bars">
            <div class="summary-item">
                <span>Projects</span>
                <div class="bar-track"><div class="bar-fill" style="width: <?= min(100, $stats['projects'] * 10) ?>%"></div></div>
                <em><?= (int) $stats['projects'] ?></em>
            </div>
            <div class="summary-item">
                <span>Experience</span>
                <div class="bar-track"><div class="bar-fill purple" style="width: <?= min(100, $stats['experience'] * 15) ?>%"></div></div>
                <em><?= (int) $stats['experience'] ?></em>
            </div>
            <div class="summary-item">
                <span>Education</span>
                <div class="bar-track"><div class="bar-fill green" style="width: <?= min(100, $stats['education'] * 20) ?>%"></div></div>
                <em><?= (int) $stats['education'] ?></em>
            </div>
        </div>
    </div>
    <div class="panel">
        <div class="panel-head">
            <h3>Recent Messages</h3>
            <a href="messages.php">View all</a>
        </div>
        <?php if (empty($recentMessages)): ?>
            <p class="empty-state">No messages yet.</p>
        <?php else: ?>
            <div class="message-list compact">
                <?php foreach ($recentMessages as $msg): ?>
                    <div class="message-item<?= empty($msg['read']) ? ' unread' : '' ?>">
                        <strong><?= e($msg['name'] ?? '') ?></strong>
                        <span><?= e($msg['email'] ?? '') ?></span>
                        <small><?= e($msg['created_at'] ?? '') ?></small>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
$pageScript = <<<HTML
<script>
const chartLabels = {$chartLabels};
const chartVisits = {$chartVisits};
const chartMessages = {$chartMessages};

new Chart(document.getElementById('visitsChart'), {
    type: 'line',
    data: {
        labels: chartLabels,
        datasets: [{
            label: 'Visits',
            data: chartVisits,
            borderColor: '#3b82f6',
            backgroundColor: 'rgba(59, 130, 246, 0.15)',
            fill: true,
            tension: 0.4,
            pointRadius: 4,
            pointBackgroundColor: '#3b82f6'
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: 'rgba(255,255,255,0.05)' } },
            x: { grid: { display: false } }
        }
    }
});

new Chart(document.getElementById('messagesChart'), {
    type: 'doughnut',
    data: {
        labels: ['Read', 'Unread'],
        datasets: [{
            data: [chartMessages.read, chartMessages.unread],
            backgroundColor: ['#22c55e', '#f59e0b'],
            borderWidth: 0
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'bottom', labels: { color: '#94a3b8' } } }
    }
});
</script>
HTML;

require __DIR__ . '/includes/footer.php';
