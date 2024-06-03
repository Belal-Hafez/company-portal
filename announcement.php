<?php
include('header.php');
require_once('db.php');

$sql = "SELECT message, created_at FROM announcements ORDER BY created_at DESC";
$result = $connection->query($sql);
?>


<div class="center">
    <?php if ($result->num_rows > 0) : ?>
        <ul class="announcement-list">
            <?php while ($row = $result->fetch_assoc()) : ?>
                <li><?php echo htmlspecialchars($row['message']); ?> - <em><?php echo date('M d, Y', strtotime($row['created_at'])); ?></em></li>
            <?php endwhile; ?>
        </ul>
    <?php else : ?>
        <p>No announcements to display.</p>
    <?php endif; ?>
</div>


<?php include('footer.php'); ?>