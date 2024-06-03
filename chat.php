<?php

include('header.php');
require_once('db.php'); // Ensure this path is correct and db.php contains proper database connection setup.

$loggedInUserId = $_SESSION['user_id']; // Ensure this session variable is correctly set upon user login.

// Enable MySQLi error reporting
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
	// Handle message sending
	if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['message_text'])) {
		$receiver_id = $_POST['receiver_id'];
		$message_text = $_POST['message_text'];

		$sendQuery = $connection->prepare("INSERT INTO messages (sender_id, receiver_id, message_text) VALUES (?, ?, ?)");
		$sendQuery->bind_param("iis", $loggedInUserId, $receiver_id, $message_text);
		$sendQuery->execute();
	}

	// Fetch all users except the logged-in user
	$usersQuery = $connection->prepare("SELECT id, firstName FROM users WHERE id != ?");
	$usersQuery->bind_param("i", $loggedInUserId);
	$usersQuery->execute();
	$result = $usersQuery->get_result();
} catch (Exception $e) {
	die('Error: ' . $e->getMessage());
}

?>


<div class="chat-container">
	<div class="users-list">
		<ul>
			<?php while ($user = $result->fetch_assoc()) : ?>
				<li><a href="?chat_with=<?php echo $user['id']; ?>"><?php echo htmlspecialchars($user['firstName']); ?></a></li>
			<?php endwhile; ?>
		</ul>
	</div>
	<div class="chat-area">
		<?php if (isset($_GET['chat_with'])) {
			$chatWithId = $_GET['chat_with'];
			$messagesQuery = $connection->prepare("SELECT * FROM messages WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?) ORDER BY timestamp ASC");
			$messagesQuery->bind_param("iiii", $loggedInUserId, $chatWithId, $chatWithId, $loggedInUserId);
			$messagesQuery->execute();
			$messages = $messagesQuery->get_result();

			echo '<ul>';
			while ($msg = $messages->fetch_assoc()) {
				echo '<li>' . htmlspecialchars($msg['message_text']) . '</li>';
			}
			echo '</ul>';

			// Message sending form
			echo '<form action="' . htmlspecialchars($_SERVER['PHP_SELF']) . '?chat_with=' . $chatWithId . '" method="post">';
			echo '<input type="hidden" name="receiver_id" value="' . $chatWithId . '">';
			echo '<textarea name="message_text" required></textarea>';
			echo '<button type="submit">Send</button>';
			echo '</form>';
		}
		?>
	</div>
</div>


<?php include('footer.php'); ?>