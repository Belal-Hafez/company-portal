<?php
include('header.php');

if (isset($_POST['logout'])) {
    // Destroy session and redirect to login page
    session_destroy();
    header("Location: login.php");
    exit;
}
?>
<div class="center">
    <div class="container">
        <h2>Logout</h2>
        <p>Are you sure you want to logout?</p>
        <form method="post">
            <button type="submit" name="logout">Logout</button>
        </form>
    </div>
</div>
<?php include('footer.php'); ?>