<?php
// index.php

require_once 'src/db.php';
require_once 'src/DatabaseSessionHandler.php';

// Set up the custom session handler
$sessionHandler = new DatabaseSessionHandler($pdo);
session_set_save_handler($sessionHandler, true);

session_start();

// Fetch tasks and readings from the database
try {
    $tasksStmt = $pdo->query("SELECT * FROM tasks ORDER BY id DESC");
    $tasks = $tasksStmt->fetchAll();

    $readingsStmt = $pdo->query("SELECT * FROM readings ORDER BY ts DESC");
    $readings = $readingsStmt->fetchAll();
} catch (PDOException $e) {
    // If tables don't exist, this will fail. For now, we can show an error.
    // In a real app, a migration system would handle table creation.
    // The railway.json should handle this on deployment.
    die("Database error: " . $e->getMessage() . ". Please ensure the database tables are created. You might need to run the initial migration/setup script.");
}


// Handle task actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    try {
        switch ($_POST['action']) {
            case 'add_task':
                if (!empty($_POST['text'])) {
                    $stmt = $pdo->prepare("INSERT INTO tasks (text, completed) VALUES (:text, false)");
                    $stmt->execute(['text' => $_POST['text']]);
                }
                break;
            case 'update_task':
                // This was not fully implemented in the original UI, but let's add the logic.
                // The UI doesn't have a form for this, so this case is not reachable.
                if (isset($_POST['id'], $_POST['text'])) {
                     $stmt = $pdo->prepare("UPDATE tasks SET text = :text WHERE id = :id");
                     $stmt->execute(['text' => $_POST['text'], 'id' => $_POST['id']]);
                }
                break;
            case 'delete_task':
                if (isset($_POST['id'])) {
                    $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = :id");
                    $stmt->execute(['id' => $_POST['id']]);
                }
                break;
            case 'complete_task':
                if (isset($_POST['id'])) {
                    $stmt = $pdo->prepare("UPDATE tasks SET completed = true WHERE id = :id");
                    $stmt->execute(['id' => $_POST['id']]);
                }
                break;
        }
        // Redirect to avoid form resubmission
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    } catch (PDOException $e) {
        die("Database error on task action: " . $e->getMessage());
    }
}

// Handle BP logging
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['systolic'])) {
    $s = (int)$_POST['systolic'];
    $d = (int)$_POST['diastolic'];
    $p = isset($_POST['pulse']) && $_POST['pulse'] !== '' ? (int)$_POST['pulse'] : null;
    $note = $_POST['note'] ?? '';

    if ($s && $d) {
        try {
            $stmt = $pdo->prepare("INSERT INTO readings (systolic, diastolic, pulse, note, ts) VALUES (:s, :d, :p, :note, NOW())");
            $stmt->execute(['s' => $s, 'd' => $d, 'p' => $p, 'note' => $note]);
            // Redirect to avoid form resubmission
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        } catch (PDOException $e) {
            die("Database error on BP logging: " . $e->getMessage());
        }
    }
}

// Handle delete/clear actions for readings via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reading_action'])) {
    try {
        switch ($_POST['reading_action']) {
            case 'delete_reading':
                if (isset($_POST['id'])) {
                    $stmt = $pdo->prepare("DELETE FROM readings WHERE id = :id");
                    $stmt->execute(['id' => $_POST['id']]);
                }
                break;
            case 'clear_readings':
                $pdo->query("DELETE FROM readings");
                break;
        }
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    } catch (PDOException $e) {
        die("Database error on reading action: " . $e->getMessage());
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
    // Fetch all readings for export, regardless of filter
    $readingsStmt = $pdo->query("SELECT * FROM readings ORDER BY ts DESC");
    while ($r = $readingsStmt->fetch(PDO::FETCH_ASSOC)) {
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
                        <?php if (!$task['completed']): ?>
                        <form method="POST" class="inline">
                            <input type="hidden" name="action" value="complete_task">
                            <input type="hidden" name="id" value="<?= $task['id'] ?>">
                            <button class="text-xs text-green-600">Complete</button>
                        </form>
                        <?php endif; ?>
                        <form method="POST" class="inline">
                            <input type="hidden" name="action" value="delete_task">
                            <input type="hidden" name="id" value="<?= $task['id'] ?>">
                            <button class="text-xs text-rose-600">Delete</button>
                        </form>
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
                <select id="filterDays" onchange="location.href = '?days=' + this.value" class="rounded-lg border p-2 text-sm">
                    <?php foreach ([7, 14, 30, 90] as $d): ?>
                        <option value="<?= $d ?>" <?= ($_GET['days'] ?? 7) == $d ? 'selected' : '' ?>><?= $d ?> days</option>
                    <?php endforeach; ?>
                </select>
                <div class="flex gap-2">
                    <a href="?export" class="text-sm rounded-lg border px-3 py-2">Export CSV</a>
                    <form method="POST" class="inline">
                        <input type="hidden" name="reading_action" value="clear_readings">
                        <button type="submit" onclick="return confirm('Clear all readings?')" class="text-sm rounded-lg border px-3 py-2 text-rose-600">Clear</button>
                    </form>
                </div>
            </div>

            <?php $days = $_GET['days'] ?? 7; $stats = getStats($readings, $days); ?>
            <div class="mt-6 grid md:grid-cols-3 gap-4">
                <div class="bg-slate-50 p-4 rounded-lg"><div class="text-xs">Avg (S/D)</div><div class="font-mono text-2xl"><?= $stats['avgS'] ?? '—' ?> / <?= $stats['avgD'] ?? '—' ?></div></div>
                <div class="bg-slate-50 p-4 rounded-lg"><div class="text-xs">Systolic (min—max)</div><div class="font-mono text-2xl"><?= $stats['minS'] ?? '—' ?>—<?= $stats['maxS'] ?? '—' ?></div></div>
                <div class="bg-slate-50 p-4 rounded-lg"><div class="text-xs">Diastolic (min—max)</div><div class="font-mono text-2xl"><?= $stats['minD'] ?? '—' ?>—<?= $stats['maxD'] ?? '—' ?></div></div>
            </div>

            <h3 class="mt-6 font-semibold">Readings</h3>
            <div class="overflow-x-auto">
            <table class="w-full text-sm mt-2">
                <thead><tr class="text-left"><th>When</th><th>S</th><th>D</th><th>Pulse</th><th>Note</th><th>Actions</th></tr></thead>
                <tbody>
                    <?php if (empty($readings)): ?><tr><td colspan="6" class="p-4 text-slate-500 text-center">No readings yet.</td></tr><?php endif; ?>
                    <?php
                        $filteredReadings = array_filter($readings, fn($r) => strtotime($r['ts']) >= strtotime("-{$days} days"));
                        foreach ($filteredReadings as $r):
                    ?>
                        <tr class="border-b">
                            <td class="py-2 px-1"><?= date('M d, Y H:i', strtotime($r['ts'])) ?></td>
                            <td class="py-2 px-1"><?= $r['systolic'] ?></td>
                            <td class="py-2 px-1"><?= $r['diastolic'] ?></td>
                            <td class="py-2 px-1"><?= $r['pulse'] ?? '—' ?></td>
                            <td class="py-2 px-1"><?= htmlspecialchars($r['note']) ?></td>
                            <td class="py-2 px-1">
                                <form method="POST" class="inline">
                                    <input type="hidden" name="reading_action" value="delete_reading">
                                    <input type="hidden" name="id" value="<?= $r['id'] ?>">
                                    <button type="submit" onclick="return confirm('Delete?')" class="text-xs text-rose-600">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            </div>
        </main>
    </div>
    <footer class="mt-6 text-xs text-center text-slate-500">Educational; not medical advice.</footer>
</body>
</html>