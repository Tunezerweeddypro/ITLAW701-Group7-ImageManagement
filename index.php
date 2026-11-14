<?php
require_once 'auth_check.php';
require_once 'db.php';

$message = '';
$error = '';

// Requirement 3: Upload image into ImageTable
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
    if ($_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['image']['tmp_name'];
        $imageData = file_get_contents($fileTmpPath);
        $currentDate = date('Y-m-d H:i:s');

        $stmt = $pdo->prepare("INSERT INTO ImageTable (Image, Date) VALUES (?, ?)");
        if ($stmt->execute([$imageData, $currentDate])) {
            $newId = $pdo->lastInsertId();
            $message = "Image #$newId uploaded to ImageTable successfully!";
        } else {
            $error = "Database insertion failed.";
        }
    } else {
        $error = "Please select an image file to upload.";
    }
}

if (isset($_GET['msg'])) {
    $message = htmlspecialchars($_GET['msg']);
}

// Requirement: Pagination
$limit = 8; // 8 items per page (matching the 2 rows of 4 in the example)
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;

$countStmt = $pdo->query("SELECT COUNT(*) FROM ImageTable");
$totalImages = $countStmt->fetchColumn();
$totalPages = ceil($totalImages / $limit);
if ($totalPages < 1) $totalPages = 1;
$offset = ($page - 1) * $limit;

// Fetch images for current page
$stmt = $pdo->prepare("SELECT Id, Image, Date FROM ImageTable ORDER BY Id ASC LIMIT ? OFFSET ?");
$stmt->bindValue(1, $limit, PDO::PARAM_INT);
$stmt->bindValue(2, $offset, PDO::PARAM_INT);
$stmt->execute();
$images = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Active Images - ImageTable</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="navbar">
        <div class="nav-brand">Image Management System</div>
        <div class="nav-links">
            <a href="index.php" class="active">Active Images (ImageTable)</a>
            <a href="backup.php">Backup Images (ImageBackupTable)</a>
            <span class="user-badge"><?= htmlspecialchars($_SESSION['username']) ?></span>
            <a href="logout.php" class="btn-logout">Logout</a>
        </div>
    </header>

    <main class="container">
        <!-- Q3: Insert Form -->
        <section class="card upload-card">
            <h2>3. Insert Image into <code>ImageTable</code></h2>
            <form action="index.php" method="POST" enctype="multipart/form-data" class="upload-form">
                <input type="file" name="image" accept="image/*" required>
                <button type="submit" class="btn btn-primary">Upload to ImageTable</button>
            </form>
        </section>

        <!-- Q4: Display Images -->
        <section class="gallery-section">
            <div class="section-title-bar">
                <h2>4. Active Images (Total: <?= $totalImages ?>)</h2>
                <span>Page <?= $page ?> of <?= $totalPages ?></span>
            </div>

            <div class="image-grid">
                <?php foreach ($images as $img): ?>
                    <div class="image-card">
                        <div class="image-box">
                            <img src="data:image/jpeg;base64,<?= base64_encode($img['Image']) ?>" alt="Image <?= $img['Id'] ?>">
                        </div>
                        <div class="image-footer">
                            <div class="image-id-number"><?= $img['Id'] ?></div>
                            <small><?= date('Y-m-d', strtotime($img['Date'])) ?></small>
                            <!-- Q5: Delete action -->
                            <a href="delete.php?id=<?= $img['Id'] ?>" 
                               class="btn-delete" 
                               onclick="return confirm('Move Image #<?= $img['Id'] ?> to ImageBackupTable?')">
                                Delete
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Pagination Links -->
            <?php if ($totalPages > 1): ?>
                <ul class="pagination">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="<?= ($i === $page) ? 'active' : '' ?>">
                            <a href="index.php?page=<?= $i ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>