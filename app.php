<?php
declare(strict_types=1);
if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
const PROJECT_NAME = 'LAB CINCO';
const GROUP_MEMBERS = ['Jorge Toapanta','Jhoely Dávila','Wilmer Adriano','Jhonatan Chávez','Wilmer López'];
const DEMO_MAIL = 'contacto@laboratoriocinco.example';
function e(mixed $value): string { return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function root_prefix(): string { $script = $_SERVER['SCRIPT_NAME'] ?? ''; return (preg_match('~/(?:ejercicio\d{2}|database)/~', $script) ? '../' : ''); }
function exercises(): array {
 return [
 ['01','Menú desplegable','Navega con un menú de opciones que se abre y se cierra con JavaScript.','Menú interactivo','◈','morado','Obligatorio'],
 ['02','Formulario de contacto','Valida cada campo antes de enviar y registra mensajes con PHP.','Comunicación','✉','coral','Obligatorio'],
 ['03','Contador de visitas','Visualiza un contador persistente administrado desde PHP.','Estadísticas','↗','azul','Obligatorio'],
 ['04','Juego de adivinanzas','Adivina un número aleatorio y recibe pistas hasta acertar.','Minijuego','?','verde','Obligatorio'],
 ['05','Galería de imágenes','Explora fotografías seleccionadas con filtro y visor ampliado.','Multimedia','▧','naranja','Obligatorio'],
 ['06','Sistema de login','Acceso de usuarios con contraseña protegida y base de datos MySQL.','Autenticación','⛨','morado','Obligatorio'],
 ['07','Conversor de unidades','Convierte distancias, masas y temperaturas mediante PHP.','Herramienta','⇄','verde','Complementario'],
 ['08','Lista de tareas','Organiza pendientes y guarda su estado en una sesión PHP.','Productividad','✓','coral','Complementario'],
 ['09','Calculadora práctica','Realiza operaciones y calcula porcentajes con validación en PHP.','Herramienta','∑','azul','Complementario'],
 ['10','Quiz de programación','Responde preguntas y recibe tu calificación calculada en PHP.','Evaluación','★','naranja','Complementario'],
 ];
}
function page_head(string $title, string $eyebrow, string $summary, string $active = ''): void {
 $base = root_prefix(); $u = e($base);
?><!doctype html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="description" content="<?php echo e($summary); ?>"><title><?php echo e($title); ?> | LAB CINCO · Grupo 5</title><link rel="icon" href="<?php echo $u; ?>assets/img/favicon.svg" type="image/svg+xml"><link rel="stylesheet" href="<?php echo $u; ?>assets/css/style.css"></head><body>
 <div class="topstrip"><div class="container topstrip-inner"><span><span class="live-dot"></span> UNIVERSIDAD BOLIVARIANA DEL ECUADOR</span><span>TAREA PRÁCTICA 3 · PROGRAMACIÓN WEB AVANZADA</span></div></div>
 <header class="header"><div class="container header-inner"><a class="brand" href="<?php echo $u; ?>index.php" aria-label="LAB CINCO: inicio"><span class="brand-mark">L<span>5</span></span><span class="brand-text">LAB <b>CINCO</b><small>TAREA PRÁCTICA 3 · GRUPO 5</small></span></a><nav class="nav" id="main-nav" aria-label="Navegación principal"><a href="<?php echo $u; ?>index.php#inicio">Inicio</a><a href="<?php echo $u; ?>index.php#ejercicios" class="<?php echo $active === 'ejercicios' ? 'active' : ''; ?>">Ejercicios</a><a href="<?php echo $u; ?>index.php#equipo">Nuestro equipo</a><a href="<?php echo $u; ?>ejercicio02/index.php">Contacto</a></nav><div class="header-actions"><span class="header-tag">10 prácticas</span><button id="nav-toggle" class="menu-btn" aria-label="Abrir menú" aria-expanded="false" aria-controls="main-nav">☰</button></div></div></header>
 <?php if ($eyebrow !== ''): ?><div class="subhero"><div class="container"><a class="backlink" href="<?php echo $u; ?>index.php#ejercicios">← Volver a los ejercicios</a><p class="eyebrow"><?php echo e($eyebrow); ?></p><h1><?php echo e($title); ?></h1><p class="subhero-desc"><?php echo e($summary); ?></p></div></div><?php endif; ?>
<main>
<?php }
function page_foot(): void { $base = e(root_prefix()); ?>
</main><footer class="footer"><div class="container"><div class="footer-grid"><div><div class="footer-logo">LAB <strong>CINCO</strong><span> / G5</span></div><p>Diez ejercicios prácticos desarrollados con HTML, CSS, JavaScript y PHP. Proyecto académico sin fines comerciales.</p></div><div><strong>Explora</strong><a href="<?php echo $base; ?>index.php#ejercicios">Todos los ejercicios</a><a href="<?php echo $base; ?>ejercicio05/index.php">Galería</a><a href="<?php echo $base; ?>ejercicio06/index.php">Inicio de sesión</a></div><div><strong>Grupo 5</strong><span>Jorge · Jhoely · Wilmer A.</span><span>Jhonatan · Wilmer L.</span><span>Universidad Bolivariana del Ecuador</span></div></div><div class="footer-bottom"><span>© 2026 · LAB CINCO · Desarrollo académico</span><span>Hecho en equipo, línea por línea <span aria-hidden="true">✦</span></span></div></div></footer><script src="<?php echo $base; ?>assets/js/main.js" defer></script></body></html><?php }
function flash(string $message, string $type='success'): void { $_SESSION['flash']=['message'=>$message,'type'=>$type]; }
function show_flash(): void { if (!empty($_SESSION['flash'])) { $f=$_SESSION['flash']; unset($_SESSION['flash']); echo '<div class="alert '.e($f['type']).'" role="status">'.e($f['message']).'</div>'; } }
function csrf_token(): string { return $_SESSION['csrf'] ??= bin2hex(random_bytes(24)); }
function csrf_valid(): bool { return is_string($_POST['csrf'] ?? null) && hash_equals(csrf_token(), $_POST['csrf']); }
