<?php
include('header.php');

if (isset($_POST['export'])) {
    require_once('db.php');
    $filename = "workers_assignments_" . date('Ymd') . ".csv";
    ob_end_clean();
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    $output = fopen('php://output', 'w');
    fputcsv($output, array('Worker Name', 'Service', 'Date and Time'));

    $sql = "SELECT users.firstName, users.lastName, services.name as serviceName, tasks.date_time 
            FROM tasks 
            JOIN users ON tasks.user_id = users.id 
            JOIN services ON tasks.service_id = services.id";
    $result = $connection->query($sql);

    while ($row = $result->fetch_assoc()) {
        fputcsv($output, array($row['firstName'] . ' ' . $row['lastName'], $row['serviceName'], $row['date_time']));
    }

    fclose($output);
    exit();
}
?>

<div class="center">
    <form method="post">
        <input type="submit" name="export" value="Export Workers and Assignments to CSV" class="button-link">
    </form>
</div>

<?php include('footer.php'); ?>