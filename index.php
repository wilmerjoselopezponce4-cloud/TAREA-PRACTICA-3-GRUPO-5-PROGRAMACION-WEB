<?php
require __DIR__.'/../includes/app.php';
$qs=[
 ['¿Qué lenguaje se usa principalmente para estructurar una página web?',['HTML','PHP','CSS'],'0'],
 ['¿Qué tecnología define colores y distribución de los elementos?',['MySQL','CSS','PHP'],'1'],
 ['¿Qué lenguaje responde a interacciones en el navegador?',['SQL','JavaScript','HTML'],'1'],
 ['¿Cuál se ejecuta en el servidor para procesar formularios en esta tarea?',['CSS','PHP','SVG'],'1'],
 ['¿Para qué se utiliza MySQL en el ejercicio 6?',['Almacenar usuarios','Editar imágenes','Animar botones'],'0']
];$score=null;$error='';
if($_SERVER['REQUEST_METHOD']==='POST'){
 if(!csrf_valid())$error='Sesión expirada, inténtalo de nuevo.';
 else {$score=0;foreach($qs as $i=>$q){$answer=(string)($_POST['q'.$i]??'');if($answer!==''&&!in_array($answer,['0','1','2'],true)){$error='Respuesta inválida detectada.';$score=null;break;}if($answer===$q[2])$score++;}if($score!==null)$_SESSION['quiz_last']=$score;}
}
page_head('Quiz de programación','EJERCICIO 10 · COMPLEMENTARIO','Comprueba tus conocimientos de HTML, CSS, JavaScript, PHP y MySQL.','ejercicios');
?><div class="exercise-content"><section class="surface"><p class="eyebrow">PONTE A PRUEBA</p><h2>¿Cuánto sabes de desarrollo web?</h2><p class="section-description">Cinco preguntas de selección única. Tu puntuación se calcula en PHP.</p><?php if($error):?><div class="alert error"><?php echo e($error); ?></div><?php endif;?><?php if($score!==null):?><div class="stats-panel"><p style="margin-bottom:8px">TU CALIFICACIÓN</p><div class="big-count"><?php echo (int)$score; ?><span style="font-size:35px">/ 5</span></div><p><?php echo $score===5?'¡Excelente! Dominaste todos los conceptos.':($score>=3?'¡Buen trabajo! Sigue practicando.':'Repasa los ejercicios y vuelve a intentarlo.'); ?></p></div><div class="center mt-25"><a href="index.php" class="btn ghost">↻ Repetir el cuestionario</a></div><?php else:?><form method="post" id="quiz-form"><input type="hidden" name="csrf" value="<?php echo e(csrf_token()); ?>"><?php foreach($qs as $i=>$q):?><fieldset class="quiz-question" style="border-left:0;border-right:0;border-top:0"><legend><h3><?php echo $i+1; ?>. <?php echo e($q[0]); ?></h3></legend><?php foreach($q[1] as $j=>$answer):?><label class="quiz-option"><input type="radio" name="q<?php echo $i; ?>" value="<?php echo $j; ?>" required><?php echo e($answer); ?></label><?php endforeach;?></fieldset><?php endforeach;?><button class="btn mt-25" type="submit">Ver mi puntuación ↗</button></form><?php endif;?></section></div><?php page_foot(); ?>
