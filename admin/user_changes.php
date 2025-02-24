<!-- <?php
require '../db.php';

$user_id = $_GET['user_id'];

$stmt = $pdo->prepare("
    SELECT c.old_name, c.new_name, c.change_date, a.name AS changed_by 
    FROM username_changes c
    JOIN table_1 a ON c.changed_by = a.id
    WHERE c.user_id = ?
");
$stmt->execute([$user_id]);
$changes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<table border="1">
    <tr>
        <th>Old Name</th>
        <th>New Name</th>
        <th>Changed By</th>
        <th>Change Date</th>
    </tr>
    <?php foreach ($changes as $change): ?>
    <tr>
        <td><?= $change['old_name'] ?></td>
        <td><?= $change['new_name'] ?></td>
        <td><?= $change['changed_by'] ?></td>
        <td><?= $change['change_date'] ?></td>
    </tr>
    <?php endforeach; ?>
</table> -->
