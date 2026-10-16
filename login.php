<?php
session_start();
require_once "config.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($username ==="" || $password === "") {
        $error = "Please enter your username and password.";
    } else {

        $sql = "SELECT id, username, password, role
                FROM users
                WHERE username = ?
                LIMIT 1";

        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            $error = "Database query failed.";
        } else {
            $stmt->bind_param("s", $username);
            $stmt->execute();

            $result = $stmt->get_result();
            $account = $result->fetch_assoc();

            if (
                $account &&
                password_verify($password, $account["password"])
            ) {
                session_regenerate_id(true);

                $_SESSION["user_id"] = $account["id"];
                $_SESSION["username"] = $account["username"];
                $_SESSION["role"] = $account["role"];

                $stmt->close();

                header("Location: dashboard.php");
                exit;
            } else {
                $error = "Invalid username or password.";
            }

            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Inventory System - Login</title>

    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>

<body class="bg-light">

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">

            <div class="card shadow">
                <div class="card-header bg-primary text-white text-center">
                    <h4>Inventory System Login</h4>
                </div>

                <div class="card-body">

                    <?php if ($error !== ""): ?>
                        <div class="alert alert-danger">
                            <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="login.php">

                        <div class="mb-3">
                            <label class="form-label">
                                Username
                            </label>

                            <input
                                type="text"
                                name="username"
                                class="form-control"
                                required
                                autocomplete="username">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                Password
                            </label>

                            <input
                                type="password"
                                name="password"
                                class="form-control"
                                required
                                autocomplete="current-password">
                        </div>

                        <button type="submit"
                                class="btn btn-primary w-100">
                            Login
                        </button>

                    </form>

                </div>
            </div>

        </div>
    </div>
</div>

</body>
</html>
