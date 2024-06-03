<?php
include('header.php');

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

require_once('db.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $query = "SELECT id, email, password FROM users WHERE email = ?";
    $stmt = $connection->prepare($query);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if ($password === $user['password']) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $user['email'];

            header("Location: index.php");
            exit;
        } else {
            $error = "Invalid email or password";
        }
    } else {
        $error = "Invalid email or password";
    }
    // Debug
    if (isset($error)) {
        echo "Error: $error";
    } else {
        echo "User found: " . $user['email'];
    }
}
?>


<body>
    <div class="center">
        <div class="container">
            <h2>Login</h2>
            <?php if (isset($error)) { ?>
                <p><?php echo $error; ?></p>
            <?php } ?>
            <form method="post">
                <label>Email:</label>
                <input type="text" name="email" required>
                <label>Password:</label>
                <input type="password" name="password" required>
                <input type="submit" value="Login">
            </form>
        </div>
    </div>
    <?php include('footer.php'); ?>