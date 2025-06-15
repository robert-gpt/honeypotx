<?php
session_start();
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = getDB();
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Inserción vulnerable a inyección SQL (intencionado para el honeypot)
    $query = "INSERT INTO usuarios (username, email, password, rol) VALUES ('$username', '$email', '$password', 'cliente')";

    try {
        $db->exec($query);
        $_SESSION['username'] = $username;
        $_SESSION['rol'] = 'cliente';
        header('Location: panel.php');
        exit();
    } catch (Exception $e) {
        $error = "Error al registrar: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Usuario</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/login.css">
</head>
<body>
    <?php include 'navbar.php'; ?>
    <div class="container mt-5" style="max-width: 400px;">
        <h2 class="mb-4 text-center">Registrarse</h2>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label for="username" class="form-label">Nombre de Usuario</label>
                <input type="text" id="username" name="username" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Correo Electrónico</label>
                <input type="email" id="email" name="email" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Contraseña</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary w-100">Registrarse</button>
        </form>
        <!-- Botón para ir a iniciar sesión -->
        <div class="mt-3 text-center">
            <p>¿Ya tienes cuenta?</p>
            <a href="login.php" class="btn btn-outline-primary">Iniciar sesión</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
