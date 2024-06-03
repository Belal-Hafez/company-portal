<?php
include('header.php');
require_once('db.php');


$sql_users = "SELECT * FROM users";
$result_users = $connection->query($sql_users);

$users = array();
if ($result_users->num_rows > 0) {
  while ($row = $result_users->fetch_assoc()) {
    $users[] = $row;
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
<div class="calendar-container">
  <?php echo build_calendar(date('m'), date('Y')); ?>
</div>


<?php include('footer.php'); ?>