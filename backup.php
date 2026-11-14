<?php
require_once 'auth_check.php';
require_once 'db.php';

$stmt = $pdo->query("SELECT Id, Image, BackupDate FROM ImageBackupTable ORDER BY Id ASC");
$backupImages = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Backup Images - ImageBackupTable</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="navbar">
        <div class="nav-brand">Image Management System</div>
        <div class="nav-links">
            <a href="index.php">Active Images (ImageTable)</a>
            <a href="backup.php" class="active">Backup Images (ImageBackupTable)</a>
            <a href="logout.php" class="btn-logout">Logout</a>
        </div>
    </header>

    <main class="container">
        <div class="assignment-header">
            <h1>6. Images in <code>ImageBackupTable</code></h1>
            <p>Deleted images preserved here (e.g. Image 4).</p>
        </div>

        <div class="image-grid">
            <?php foreach ($backupImages as $img): ?>
                <div class="image-card backup-card">
                    <div class="image-box">
                        <img src="data:image/jpeg;base64,<?= base64_encode($img['Image']) ?>" alt="Backup Image <?= $img['Id'] ?>">
                    </div>
                    <div class="image-footer">
                        <div class="image-id-number"><?= $img['Id'] ?></div>
                        <small>Backed up: <?= date('Y-m-d H:i', strtotime($img['BackupDate'])) ?></small>
                        <a href="restore.php?id=<?= $img['Id'] ?>" class="btn-restore">Restore</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </main>
</body>
</html>