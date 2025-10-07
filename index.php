<?php
session_start();

// Initialize data
$tasks = $_SESSION['tasks'] ?? [];
$readings = $_SESSION['readings'] ?? [];

// Handle task actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'add_task':
            $tasks[] = ['id' => time(), 'text' => $_POST['text'], 'completed' => false];
            break;
        case 'update_task':
            foreach ($tasks as &$task) {
                if ($task['id'] == $_POST['id']) {
                    $task['text'] = $_POST['text'];
                    break;
                }
            }
            break;
        case 'delete_task':
            $tasks = array_filter($tasks, fn($t) => $t['id'] != $_POST['id']);
            break;
        case 'complete_task':
            foreach ($tasks as &$task) {
                if ($task['id'] == $_POST['id']) {
                    $task['completed'] = true;
                    break;
                }
            }
            break;
    }
    $_SESSION['tasks'] = $tasks;
}

// Handle BP logging
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['systolic'])) {
    $s = (int)$_POST['systolic'];
    $d = (int)$_POST['diastolic'];
    $p = isset($_POST['pulse']) ? (int)$_POST['pulse'] : null;
    $note = $_POST['note'] ?? '';
    if ($s && $d) {
        $readings[] = [
            'id' => time(),
            'systolic' => $s,
            'diastolic' => $d,
            'pulse' => $p,
            'note' => $note,
            'ts' => date('c')
        ];
        $_SESSION['readings'] = $readings;
    }
}

// Stats function
function getStats($readings, $days = 7) {
    $cutoff = strtotime("-$days days");
    $window = array_filter($readings, fn($r) => strtotime($r['ts']) >= $cutoff);
    if (empty($window)) return ['avgS' => null, 'avgD' => null, 'minS' => null, 'maxS' => null, 'minD' => null, 'maxD' => null];
    $systs = array_column($window, 'systolic');
    $diasts = array_column($window, 'diastolic');
    $avg = fn($arr) => round(array_sum($arr) / count($arr));
    return [
        'avgS' => $avg($systs),
        'avgD' => $avg($diasts),
        'minS' => min($systs),
        'maxS' => max($systs),
        'minD' => min($diasts),
        'maxD' => max($diasts)
    ];
}

// Export CSV
if (isset($_GET['export'])) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="bp_readings.csv"');
    $out = fopen('php://output', 'w');
    fputcsv($out, ['timestamp', 'systolic', 'diastolic', 'pulse', 'note']);
    foreach ($readings as $r) {
        fputcsv($out, [$r['ts'], $r['systolic'], $r['diastolic'], $r['pulse'] ?? '', $r['note']]);
    }
    fclose($out);
    exit;
}

// HTML UI (with Tailwind classes, assume CDN)
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Task App with BP Monitoring</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gray-50 p-6">
    <div class="max-w-5xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Sidebar: Tips -->
        <aside class="bg-white rounded-2xl p-6 shadow-sm">
            <h1 class="text-lg font-bold">HEALTHbeat — Monitoring BP at Home</h1>
            <p class="mt-1 text-sm text-slate-500">Track tasks and BP readings.</p>
            <section class="mt-5 text-sm">
                <p>A single high reading can be misleading; home monitoring reveals patterns.</p>
                <a href="https://www.health.harvard.edu/heart-health/track-your-blood-pressure-at-home-the-right-way" class="text-xs underline" target="_blank">Read full article</a>
            </section>
            <hr class="my-4">
            <h3 class="font-semibold">Quick Tips</h3>
            <ul class="mt-2 space-y-2 text-sm">
                <li>• Use validated upper-arm monitor.</li>
                <li>• Measure morning & evening.</li>
                <li>• Avoid caffeine/smoking/exercise 30min before.</li>
                <li>• Sit quietly 5min; arm at heart level.</li>
                <li>• Take 2-3 readings; record patterns.</li>
                <li>• Yearly calibration; avoid wrist monitors.</li>
            </ul>
            <hr class="my-4">
            <p class="text-xs text-slate-500">Educational only; consult physician.</p>
        </aside>

        <!-- Main: Tasks & BP Logger -->
        <main class="md:col-span-2 bg-white rounded-2xl p-6 shadow-sm">
            <h2 class="text-xl font-bold">Tasks</h2>
            <form method="POST" class="mt-4 grid gap-3">
                <input type="hidden" name="action" value="add_task">
                <input name="text" placeholder="New task" class="w-full rounded-lg border p-2">
                <button type="submit" class="rounded-lg bg-emerald-500 text-white px-4 py-2">Add</button>
            </form>
            <ul class="mt-6 space-y-2">
                <?php foreach ($tasks as $task): ?>
                    <li class="flex items-center gap-2">
                        <span <?= $task['completed'] ? 'class="line-through"' : '' ?>><?= htmlspecialchars($task['text']) ?></span>
                        <form method="POST" class="inline"><input type="hidden" name="action" value="complete_task"><input type="hidden" name="id" value="<?= $task['id'] ?>"><button class="text-xs text-green-600">Complete</button></form>
                        <form method="POST" class="inline"><input type="hidden" name="action" value="delete_task"><input type="hidden" name="id" value="<?= $task['id'] ?>"><button class="text-xs text-rose-600">Delete</button></form>
                    </li>
                <?php endforeach; ?>
            </ul>

            <hr class="my-6">

            <h2 class="text-xl font-bold">BP Logger</h2>
            <form method="POST" class="mt-4 grid sm:grid-cols-4 gap-3">
                <label><span class="text-xs">Systolic</span><input name="systolic" type="number" class="mt-1 w-full rounded-lg border p-2" placeholder="120"></label>
                <label><span class="text-xs">Diastolic</span><input name="diastolic" type="number" class="mt-1 w-full rounded-lg border p-2" placeholder="80"></label>
                <label><span class="text-xs">Pulse</span><input name="pulse" type="number" class="mt-1 w-full rounded-lg border p-2" placeholder="68"></label>
                <div><span class="text-xs">Note</span><div class="mt-1 flex gap-2"><input name="note" class="flex-1 rounded-lg border p-2" placeholder="After walking"><button type="submit" class="rounded-lg bg-emerald-500 text-white px-4 py-2">Save</button></div></div>
            </form>

            <div class="mt-6 flex justify-between">
                <select id="filterDays" class="rounded-lg border p-2 text-sm">
                    <option value="7">7 days</option><option value="14">14 days</option><option value="30">30 days</option><option value="90">90 days</option>
                </select>
                <div class="flex gap-2">
                    <a href="?export" class="text-sm rounded-lg border px-3 py-2">Export CSV</a>
                    <button onclick="if(confirm('Clear readings?')) location.href='?clear'" class="text-sm rounded-lg border px-3 py-2 text-rose-600">Clear</button>
                </div>
            </div>

            <?php $days = $_GET['days'] ?? 7; $stats = getStats($readings, $days); ?>
            <div class="mt-6 grid md:grid-cols-3 gap-4">
                <div class="bg-slate-50 p-4 rounded-lg"><div class="text-xs">Avg (S/D)</div><div class="font-mono text-2xl"><?= $stats['avgS'] ?? '—' ?> / <?= $stats['avgD'] ?? '—' ?></div></div>
                <div class="bg-slate-50 p-4 rounded-lg"><div class="text-xs">Systolic (min—max)</div><div class="font-mono text-2xl"><?= $stats['minS'] ?? '—' ?>—<?= $stats['maxS'] ?? '—' ?></div></div>
                <div class="bg-slate-50 p-4 rounded-lg"><div class="text-xs">Diastolic (min—max)</div><div class="font-mono text-2xl"><?= $stats['minD'] ?? '—' ?>—<?= $stats['maxD'] ?? '—' ?></div></div>
            </div>

            <h3 class="mt-6 font-semibold">Readings</h3>
            <table class="w-full text-sm">
                <thead><tr><th>When</th><th>S</th><th>D</th><th>Pulse</th><th>Note</th><th>Actions</th></tr></thead>
                <tbody>
                    <?php if (empty($readings)): ?><tr><td colspan="6" class="p-4 text-slate-500">No readings yet.</td></tr><?php endif; ?>
                    <?php foreach ($readings as $r): ?>
                        <tr><td><?= date('c', $r['id']) ?></td><td><?= $r['systolic'] ?></td><td><?= $r['diastolic'] ?></td><td><?= $r['pulse'] ?? '—' ?></td><td><?= htmlspecialchars($r['note']) ?></td><td><button onclick="if(confirm('Delete?')) location.href='?delete=<?= $r['id'] ?>'" class="text-xs text-rose-600">Delete</button></td></tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </main>
    </div>
    <footer class="mt-6 text-xs text-center text-slate-500">Educational; not medical advice.</footer>
</body>
</html>
<?php
// Handle delete/clear (simplified)
if (isset($_GET['delete'])) {
    $readings = array_filter($readings, fn($r) => $r['id'] != $_GET['delete']);
    $_SESSION['readings'] = $readings;
}
if (isset($_GET['clear'])) {
    $_SESSION['readings'] = [];
}
?>
