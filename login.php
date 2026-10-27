<?php
declare(strict_types=1);
require __DIR__.'/../includes/app.php';
require __DIR__.'/../database/config.php';

if (isset($_SESSION['auth_user'])) {
    header('Location: index.php');
    exit;
}
$error = '';
$notice = '';
$local = g5_is_local();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_valid()) {
        $error = 'La sesión ha expirado. Actualiza el formulario y vuelve a intentarlo.';
    } else {
        $email = strtolower(trim((string)($_POST['email'] ?? '')));
        $password = (string)($_POST['password'] ?? '');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
            $error = 'Ingresa un correo y una contraseña válidos.';
        } else {
            $mysqlError = false;
            try {
                // En XAMPP local configura automáticamente la base si MySQL está encendido.
                $pdo = g5_login_connection();
                $stmt = $pdo->prepare('SELECT id,nombre,email,password_hash FROM usuarios WHERE email = :email LIMIT 1');
                $stmt->execute(['email' => $email]);
                $user = $stmt->fetch();
                if ($user && password_verify($password, (string)$user['password_hash'])) {
                    session_regenerate_id(true);
                    $_SESSION['auth_user'] = [
                        'id' => (int)$user['id'],
                        'nombre' => $user['nombre'],
                        'email' => $user['email'],
                        'mode' => 'mysql',
                    ];
                    header('Location: index.php');
                    exit;
                }
                $error = 'Correo o contraseña incorrectos.';
            } catch (Throwable $ex) {
                $mysqlError = true;
            }
            // Si no arranca MySQL, el proyecto puede demostrarse en el PC del alumno.
            // Este acceso alternativo está bloqueado para conexiones externas.
            if ($mysqlError) {
                $demoUser = g5_demo_credentials($email, $password);
                if ($demoUser !== null) {
                    session_regenerate_id(true);
                    $_SESSION['auth_user'] = $demoUser + ['mode' => 'demo'];
                    header('Location: index.php');
                    exit;
                }
                $error = $local
                    ? 'No hay conexión MySQL y los datos no coinciden con una cuenta de prueba. Inicia MySQL en XAMPP o utiliza una de las credenciales ficticias mostradas abajo.'
                    : 'El servidor no pudo validar el acceso en MySQL. Contacta con quien administra este sitio.';
            }
        }
    }
}

if ($local) {
    try {
        db();
        $notice = 'MySQL responde. Al iniciar sesión se crearán, si aún no existen, la base y los usuarios de prueba.';
        $noticeLevel = 'success';
    } catch (Throwable $ex) {
        $notice = 'MySQL no está disponible. Puedes entrar con las cuentas de prueba en modo demostración local. Para utilizar MySQL, enciéndelo en XAMPP y abre el instalador.';
        $noticeLevel = 'info';
    }
} else {
    $notice = 'Para usar este formulario en un servidor público se necesita configurar MySQL y crear cuentas privadas.';
    $noticeLevel = 'info';
}

page_head('Iniciar sesión','EJERCICIO 06 · AUTENTICACIÓN','Inicia sesión con PHP, MySQL y cuentas ficticias de la práctica.','ejercicios');
?>
<div class="exercise-content"><div class="login-wrap"><section class="surface">
    <div class="login-avatar">⛨</div>
    <h2 class="center">Bienvenido de nuevo</h2>
    <p class="section-description center">Accede al espacio privado del Grupo 5.</p>
    <?php show_flash(); ?>
    <?php if ($error): ?><div class="alert error" role="alert"><?php echo e($error); ?></div><?php endif; ?>
    <?php if ($notice): ?><div class="alert <?php echo e($noticeLevel); ?>" role="status"><?php echo e($notice); ?></div><?php endif; ?>
    <form method="post">
       <input type="hidden" name="csrf" value="<?php echo e(csrf_token()); ?>">
       <div class="field"><label for="email">Correo electrónico</label><input id="email" type="email" name="email" required autocomplete="username" placeholder="wilmer.lopez@grupo5.local" maxlength="150" value="<?php echo e($_POST['email'] ?? ''); ?>"></div>
       <div class="field"><label for="password">Contraseña</label><input id="password" type="password" name="password" required autocomplete="current-password" placeholder="Tu contraseña"></div>
       <button class="btn" type="submit" style="width:100%">Iniciar sesión ↗</button>
    </form>
    <div class="info-card mt-25"><h3>Usuarios ficticios para pruebas</h3>
        <p>Contraseña común: <code>Grupo5_2026!</code></p>
        <ul class="simple-list"><?php foreach (g5_demo_accounts() as $item): ?><li><?php echo e($item['nombre'].' · '.$item['email']); ?></li><?php endforeach; ?></ul>
        <p><a class="text-link" href="../database/instalar.php">Configurar MySQL desde XAMPP ↗</a></p>
        <p class="hint">El modo demostración sin MySQL funciona únicamente en localhost. Los inicios de sesión que utilicen ese modo se identifican claramente dentro del panel.</p>
    </div>
</section></div></div>
<?php page_foot(); ?>
