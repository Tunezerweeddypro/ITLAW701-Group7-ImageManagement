<?php
// delete.php - Handles moving deleted image to ImageBackupTable
require_once 'auth_check.php';
require_once 'db.php';

$id = isset($_GET['id']) && is_numeric($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    try {
        $pdo->beginTransaction();

        // 1. Fetch record from ImageTable
        $select = $pdo->prepare("SELECT Id, Image, Date FROM ImageTable WHERE Id = ?");
        $select->execute([$id]);
        $record = $select->fetch();

        if ($record) {
            // 2. Insert into ImageBackupTable with the EXACT same original Id
            $backup = $pdo->prepare("REPLACE INTO ImageBackupTable (Id, Image, Date, BackupDate) VALUES (?, ?, ?, NOW())");
            $backup->execute([$record['Id'], $record['Image'], $record['Date']]);

            // 3. Delete from ImageTable
            $delete = $pdo->prepare("DELETE FROM ImageTable WHERE Id = ?");
            $delete->execute([$id]);

            $pdo->commit();
            header("Location: index.php?msg=" . urlencode("Image #$id moved to ImageBackupTable."));
            exit();
        } else {
            $pdo->rollBack();
            header("Location: index.php?msg=" . urlencode("Image not found."));
            exit();
        }
    } catch (Exception $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        die("Error: " . $e->getMessage());
    }
}
header("Location: index.php");
exit();
?>