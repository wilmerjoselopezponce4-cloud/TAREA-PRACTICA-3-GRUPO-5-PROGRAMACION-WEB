<?php
declare(strict_types=1);
require dirname(__DIR__).'/includes/app.php';
require __DIR__.'/config.php';
if (!g5_is_local()) {
    http_response_code(403);
    exit('Instalación disponible solo desde localhost. La configuración MySQL en un hosting debe realizarse por separado.');
}

$feedback = '';
$level = 'info';
$status = '';
try {
    $pdo = db();
    $status = 'Conexión MySQL disponible. Puedes instalar o revisar las cuentas de demostración.';
} catch (Throwable $e) {
    $status = 'MySQL todavía no está conectado. Inicia MySQL desde el panel XAMPP antes de instalar.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_valid()) {
        $feedback = 'La solicitud ha caducado. Actualiza la página y vuelve a intentarlo.';
        $level = 'error';
    } else {
        try {
            g5_install_mysql();
            $feedback = '¡Instalación completada! Ya puedes ingresar con wilmer.lopez@grupo5.local y Grupo5_2026! usando MySQL.';
            $level = 'success';
            $status = 'Base de datos y cuentas listas.';
        } catch (Throwable $e) {
            $feedback = 'No se pudo instalar MySQL. Comprueba que el indicador de MySQL en XAMPP esté verde y el puerto configurado sea el correcto (habitualmente 3306).';
            $level = 'error';
        }
    }
}
page_head('Configurar MySQL','EJERCICIO 06 · INSTALACIÓN','Configuración de la base y usuarios ficticios desde XAMPP.','ejercicios');
?>
<div class="exercise-content"><section class="surface">
  <h2>Preparar el inicio de sesión</h2>
  <p class="section-description">El proyecto usa MySQL para guardar usuarios. En tu computadora la instalación se realiza con un botón.</p>
  <?php if ($feedback): ?><div class="alert <?php echo e($level); ?>" role="status"><?php echo e($feedback); ?></div><?php endif; ?>
  <div class="info-card"><h3>Estado de conexión</h3><p><?php echo e($status); ?></p></div>
  <ol class="simple-list">
     <li>Abre el panel de control XAMPP.</li>
     <li>Inicia <strong>Apache</strong> y <strong>MySQL</strong>. Ambos deben mostrar el indicador verde.</li>
     <li>Haz clic en el botón de abajo para crear la base y cinco usuarios de prueba.</li>
     <li>Abre el login y usa una de las cuentas listadas.</li>
  </ol>
  <form method="post" style="margin-top:24px">
      <input type="hidden" name="csrf" value="<?php echo e(csrf_token()); ?>">
      <button type="submit" class="btn">Instalar o verificar cuentas MySQL ↗</button>
  </form>
  <div class="info-card mt-25"><h3>Usuarios ficticios</h3>
      <ul class="simple-list"><?php foreach (g5_demo_accounts() as $account): ?><li><?php echo e($account['nombre'].' · '.$account['email']); ?></li><?php endforeach; ?></ul>
      <p>Contraseña: <code>Grupo5_2026!</code></p>
  </div>
  <p class="hint mt-25">Si MySQL está apagado, el login admite un modo de demostración <strong>solo en localhost</strong>. Para evaluar el ejercicio con MySQL, realiza estos pasos.</p>
  <a class="btn alt mt-25" href="../ejercicio06/login.php">Volver al inicio de sesión →</a>
</section></div>
<?php page_foot(); ?>
