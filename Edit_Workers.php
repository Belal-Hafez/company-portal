<?php
include('header.php');
require_once('db.php');

$updateSuccess = false;

// Handle the update form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $taskId = $_POST['taskId'];
    $userId = $_POST['userId'];
    $serviceId = $_POST['serviceId'];
    $dateTime = $_POST['dateTime'];

    $update_sql = "UPDATE tasks SET user_id = ?, service_id = ?, date_time = ? WHERE id = ?";
    $update_stmt = $connection->prepare($update_sql);
    $update_stmt->bind_param("iisi", $userId, $serviceId, $dateTime, $taskId);
    if ($update_stmt->execute()) {
        $updateSuccess = true;
    }
}

// Fetch assignments for displaying
$sql = "SELECT tasks.id, users.firstName, users.lastName, services.name as serviceName, tasks.date_time 
        FROM tasks 
        JOIN users ON tasks.user_id = users.id 
        JOIN services ON tasks.service_id = services.id";
$result = $connection->query($sql);
?>

<div class="form-container">
    <h2>Worker Assignments</h2>
    <table>
        <thead>
            <tr>
                <th>Worker Name</th>
                <th>Service</th>
                <th>Date/Time</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $row['firstName'] . ' ' . $row['lastName']; ?></td>
                    <td><?php echo $row['serviceName']; ?></td>
                    <td><?php echo $row['date_time']; ?></td>
                    <td>
                        <a href="?edit_id=<?php echo $row['id']; ?>" class="button-link">Edit</a>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <?php if (isset($_GET['edit_id'])) : ?>
        <?php
        $edit_id = $_GET['edit_id'];
        $edit_sql = "SELECT user_id, service_id, date_time FROM tasks WHERE id = ?";
        $edit_stmt = $connection->prepare($edit_sql);
        $edit_stmt->bind_param("i", $edit_id);
        $edit_stmt->execute();
        $edit_result = $edit_stmt->get_result();
        $edit_data = $edit_result->fetch_assoc();
        ?>
        <h3>Edit Assignment</h3>
        <form method="post" action="">
            <input type="hidden" name="taskId" value="<?php echo $edit_id; ?>">
            <div class="select-container">
                <label for="userId">Select User:</label>
                <select id="userId" name="userId">
                    <?php
                    $users = $connection->query("SELECT id, firstName, lastName FROM users");
                    while ($user = $users->fetch_assoc()) {
                        $selected = $edit_data['user_id'] == $user['id'] ? 'selected' : '';
                        echo "<option value='{$user['id']}' {$selected}>{$user['firstName']} {$user['lastName']}</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="select-container">
                <label for="serviceId">Select Service:</label>
                <select id="serviceId" name="serviceId">
                    <?php
                    $services = $connection->query("SELECT id, name FROM services");
                    while ($service = $services->fetch_assoc()) {
                        $selected = $edit_data['service_id'] == $service['id'] ? 'selected' : '';
                        echo "<option value='{$service['id']}' {$selected}>{$service['name']}</option>";
                    }
                    ?>
                </select>
            </div>
            <label for="dateTime">Date/Time:</label>
            <input type="datetime-local" id="dateTime" name="dateTime" value="<?php echo $edit_data['date_time']; ?>">
            <button type="submit">Update</button>
        </form>
    <?php endif; ?>
</div>

<?php if ($updateSuccess) : ?>
    <div id="successMessage" class="success-message">
        Assignment Updated Successfully!
    </div>
    <script>
        setTimeout(function() {
            var successMessage = document.getElementById('successMessage');
            if (successMessage) {
                successMessage.style.display = 'none';
            }
        }, 5000);
    </script>
<?php endif; ?>

<?php include('footer.php'); ?>