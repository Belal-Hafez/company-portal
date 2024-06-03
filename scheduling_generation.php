<?php
include('header.php');
require_once('db.php');

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Fetch users from the database
$sql_users = "SELECT * FROM users";
$result_users = $connection->query($sql_users);

$users = array();
if ($result_users->num_rows > 0) {
    while ($row = $result_users->fetch_assoc()) {
        $users[] = $row;
    }
}

// Fetch services from the database
$sql_services = "SELECT id, name FROM services";
$result_services = $connection->query($sql_services);

$services = array();
if ($result_services->num_rows > 0) {
    while ($row = $result_services->fetch_assoc()) {
        $services[] = $row;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $userId = $_POST['userId'];
    $serviceId = $_POST['serviceId'];
    $dateTime = $_POST['dateTime'];

    $sql_insert = "INSERT INTO tasks (user_id, service_id, date_time) VALUES (?, ?, ?)";
    $stmt = $connection->prepare($sql_insert);
    $stmt->bind_param("iis", $userId, $serviceId, $dateTime);
    if ($stmt->execute()) {
        echo "<div id='successMessage' class='success-message'>Service assigned successfully!</div>
              <script>
                  setTimeout(function() {
                      document.getElementById('successMessage').style.display = 'none';
                  }, 5000);
              </script>";
    } else {
        echo "Error inserting data: " . $stmt->error;
    }
}

function fetch_tasks($month, $year)
{
    global $connection;
    $start_date = "$year-$month-01";
    $end_date = date("Y-m-t", strtotime($start_date));
    $sql = "SELECT DAY(tasks.date_time) as day, 
               users.firstName, 
               users.lastName, 
               services.name as serviceName 
        FROM tasks 
        JOIN users ON tasks.user_id = users.id 
        JOIN services ON tasks.service_id = services.id 
        WHERE tasks.date_time BETWEEN '$start_date' AND '$end_date'";
    $result = $connection->query($sql);
    $tasks = [];
    while ($row = $result->fetch_assoc()) {
        $tasks[$row['day']][] = $row;
    }
    return $tasks;
}

function build_calendar($month, $year)
{
    $daysOfWeek = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
    $firstDayOfMonth = mktime(0, 0, 0, $month, 1, $year);
    $numberDays = date('t', $firstDayOfMonth);
    $dateComponents = getdate($firstDayOfMonth);
    $monthName = $dateComponents['month'];
    $dayOfWeek = $dateComponents['wday'];

    $tasks = fetch_tasks($month, $year);

    $calendar = "<h2>$monthName $year</h2>";
    $calendar .= "<table border='1'>";
    $calendar .= "<thead><tr>";
    foreach ($daysOfWeek as $day) {
        $calendar .= "<th>$day</th>";
    }
    $calendar .= "</tr></thead><tbody><tr>";

    if ($dayOfWeek > 0) {
        $calendar .= str_repeat('<td></td>', $dayOfWeek);
    }

    $currentDay = 1;
    while ($currentDay <= $numberDays) {
        if ($dayOfWeek == 7) {
            $dayOfWeek = 0;
            $calendar .= "</tr><tr>";
        }

        $calendar .= "<td>";
        if (isset($tasks[$currentDay])) {
            foreach ($tasks[$currentDay] as $task) {
                $userName = $task['firstName'] . ' ' . $task['lastName'];
                $serviceName = $task['serviceName'];
                $calendar .= "<small>User: $userName, Service: $serviceName</small><br>";
            }
        }
        $calendar .= "$currentDay</td>";
        $currentDay++;
        $dayOfWeek++;
    }

    if ($dayOfWeek != 7) {
        $remainingDays = 7 - $dayOfWeek;
        $calendar .= str_repeat('<td></td>', $remainingDays);
    }

    $calendar .= "</tr></tbody></table>";
    return $calendar;
}
?>

<div id="successMessage" class="success-message" style="display: none;">Service assigned successfully!</div>
<div class="container form-container">
    <h2>Assign Service to User</h2>
    <form method="post" action="">
        <div class="select-container">
            <label for="userId">Select User:</label>
            <select name="userId" required>
                <?php foreach ($users as $user) { ?>
                    <option value="<?php echo $user['id']; ?>"><?php echo $user['firstName'] . ' ' . $user['lastName']; ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="select-container">
            <label for="serviceId">Select Service:</label>
            <select name="serviceId" required>
                <?php foreach ($services as $service) { ?>
                    <option value="<?php echo $service['id']; ?>"><?php echo $service['name']; ?></option>
                <?php } ?>
            </select>
        </div>
        <label for="dateTime">Select Date and Time:</label>
        <input type="datetime-local" name="dateTime" required>
        <button type="submit">Assign Service</button>
    </form>
</div>

<!-- Display the calendar -->
<div class="calendar-container">
    <?php echo build_calendar(date('m'), date('Y')); ?>
</div>

<?php include('footer.php'); ?>