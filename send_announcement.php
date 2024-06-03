<?php
include('header.php');
require_once('db.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $announcement = $_POST['announcement'];
    if (!empty($announcement)) {
        $sql = "INSERT INTO announcements (message) VALUES (?)";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("s", $announcement);
        $stmt->execute();
        $submitted = true;
    }
}
?>

<div class="center">
    <form method="post" action="">
        <textarea name="announcement" rows="4" cols="50" required></textarea><br>
        <input type="submit" value="Send Announcement" class="button-link">
        <?php if (!empty($submitted)) : ?>
            <div id="successMessage" class="announcement-success">Announcement sent successfully!</div>
            <script>
                setTimeout(function() {
                    var successMessage = document.getElementById('successMessage');
                    successMessage.style.display = 'none';
                }, 5000);
            </script>
        <?php endif; ?>
    </form>
</div>

<?php include('footer.php'); ?>