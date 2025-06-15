<?php
session_start();
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = getDB();
    $username = $_POST['username'];
    $password = $_POST['password'];

    $ip = $_SERVER['REMOTE_ADDR'];

    $query = "SELECT * FROM usuarios WHERE username = '$username' AND password = '$password'";

    error_log("[HONEYX] IP=$ip - Intento de login con usuario='$username' y contraseña='$password' - Query=$query");

    $result = $db->query($query);
    $user = $result->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $_SESSION['username'] = $user['username'];
        $_SESSION['rol'] = $user['rol'];
        header('Location: panel.php');
        exit();
    } else {
        $error = "Usuario o contraseña incorrectos.";
    }
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/login.css">
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container mt-5" style="max-width: 400px;">
    <h2 class="mb-4 text-center">Iniciar Sesión</h2>
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>
    <form method="POST">
        <div class="mb-3">
            <label for="username" class="form-label">Usuario</label>
            <input type="text" id="username" name="username" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Contraseña</label>
            <input type="password" id="password" name="password" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary w-100">Entrar</button>
    </form>

    <!-- Botón para ir a registrarse -->
    <div class="mt-3 text-center">
        <p>¿No tienes cuenta?</p>
        <a href="register.php" class="btn btn-outline-primary">Registrarse</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
