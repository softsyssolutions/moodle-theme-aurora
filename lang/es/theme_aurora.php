<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Cadenas del tema Aurora (Español).
 *
 * @package    theme_aurora
 * @copyright  2026 SoftSys Solutions
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Plugin + página de ajustes.
$string['pluginname'] = 'Aurora';
$string['choosereadme'] = 'Aurora es un tema de Moodle moderno y gamificado. Un hijo liviano de Boost con barra lateral, barra superior y elementos de progreso de aprendizaje, construido a partir de la maqueta "Aprende Online — Aurora".';
$string['configtitle'] = 'Ajustes de Aurora';
$string['generalsettings'] = 'General';
$string['advancedsettings'] = 'Avanzado';
$string['chromesettings'] = 'Interfaz de Aurora';

// Ajustes con paridad de Boost.
$string['preset'] = 'Preajuste del tema';
$string['preset_desc'] = 'Elegí un preajuste para cambiar de forma general la apariencia del tema.';
$string['presetfiles'] = 'Archivos de preajuste adicionales';
$string['presetfiles_desc'] = 'Los archivos de preajuste se pueden usar para alterar drásticamente la apariencia del tema. Consultá <a href="https://docs.moodle.org/dev/Boost_Presets">Boost presets</a> para más información sobre cómo crear y compartir tus propios preajustes.';
$string['unaddableblocks'] = 'Bloques no agregables';
$string['unaddableblocks_desc'] = 'Los bloques indicados no son necesarios con este tema y no aparecerán en el menú "Agregar un bloque".';
$string['logo'] = 'Logo';
$string['logo_desc'] = 'El logo que se muestra en la cabecera de la barra lateral y en la página de acceso. Si está vacío, se usa el nombre corto del sitio.';
$string['backgroundimage'] = 'Imagen de fondo';
$string['backgroundimage_desc'] = 'La imagen que se muestra como fondo del sitio. La imagen que subas aquí reemplazará a la del preajuste.';
$string['loginbackgroundimage'] = 'Imagen de fondo de la página de acceso';
$string['loginbackgroundimage_desc'] = 'La imagen que se muestra como fondo de la página de acceso.';
$string['brandcolor'] = 'Color de marca';
$string['brandcolor_desc'] = 'El color de acento. Define el verde primario en botones, enlaces y chrome.';
$string['accentcolor'] = 'Color de acento';
$string['accentcolor_desc'] = 'El amarillo sticky usado en Continuar y logros.';
$string['secondcolor'] = 'Color secundario';
$string['secondcolor_desc'] = 'El verde sticky usado en columnas de cursos y tarjetas.';
$string['rawscsspre'] = 'SCSS inicial sin procesar';
$string['rawscsspre_desc'] = 'Usá este campo para incluir código SCSS o CSS que se inyectará antes que todo lo demás. La mayoría de las veces lo vas a usar para definir variables.';
$string['rawscss'] = 'SCSS sin procesar';
$string['rawscss_desc'] = 'Usá este campo para incluir código SCSS o CSS que se inyectará al final de la hoja de estilos.';

// Ajustes del chrome de Aurora.
$string['greeting'] = 'Texto de saludo';
$string['greeting_desc'] = 'El saludo que aparece en el héroe de inicio y en la barra superior. El nombre del usuario se agrega automáticamente.';
$string['default_greeting'] = '¡Hola de nuevo';
$string['showstreak'] = 'Mostrar tarjeta de racha';
$string['showstreak_desc'] = 'Mostrar la tarjeta de racha de aprendizaje fijada al pie de la barra lateral.';
$string['streaktarget'] = 'Meta de racha (días)';
$string['streaktarget_desc'] = 'La cantidad de días que se muestran como meta en la barra de segmentos de la racha.';
$string['weeklygoal'] = 'Meta semanal (días)';
$string['weeklygoal_desc'] = 'La cantidad objetivo de días activos por semana que usa el anillo de meta semanal.';
$string['showweeklygoal'] = 'Mostrar anillo de meta semanal';
$string['showweeklygoal_desc'] = 'Mostrar el anillo de progreso de la meta semanal en el panel.';
$string['recommendedheading'] = 'Título de recomendados';
$string['recommendedheading_desc'] = 'Título de la sección de cursos recomendados en la página de inicio.';
$string['default_recommendedheading'] = 'Recomendados para vos';
$string['recommendedsubheading'] = 'Subtítulo de recomendados';
$string['recommendedsubheading_desc'] = 'Subtítulo de la sección de cursos recomendados en la página de inicio.';
$string['default_recommendedsubheading'] = 'Según tu actividad reciente';

// Copia del chrome de Aurora (renderizada por plantillas/renderer).
$string['streakcardtitle'] = 'Racha de aprendizaje';
$string['streakcardunit'] = 'días';
$string['streakcardcta'] = '¡Seguí así! {$a} días más para tu logro semanal.';
$string['streakcardhintdone'] = '¡Meta alcanzada! Gran racha esta semana.';
$string['weeklygoaltitle'] = 'Meta semanal';
$string['weeklygoaldays'] = '{$a->done} de {$a->total} días';
$string['weeklygoalcta'] = 'Un día más y completás tu objetivo de la semana.';
$string['continuelearning'] = 'Seguí donde lo dejaste';
$string['panelsubtitle'] = 'Subtítulo del panel';
$string['panelsubtitle_desc'] = 'El subtítulo bajo el encabezado "Mi panel" en el dashboard.';
$string['default_panelsubtitle'] = 'Acá está tu progreso de aprendizaje de esta semana.';
$string['streakcount'] = 'Racha actual (días)';
$string['streakcount_desc'] = 'La racha actual de días mostrada en la tarjeta lateral y el pill superior.';
$string['continuelearningheading'] = 'Seguí donde lo dejaste';
$string['recommendedcourses'] = 'Recomendados para vos';
$string['stathours'] = 'Horas';
$string['statsinprogress_default'] = '3';
$string['statscompleted_default'] = '12';
$string['statsawards_default'] = '5';
$string['statshours_default'] = '48';
$string['statsinprogress'] = 'Stats — cursos en progreso';
$string['statsinprogress_desc'] = 'Número mostrado en el stat "En progreso" de la página de inicio.';
$string['statscompleted'] = 'Stats — cursos completados';
$string['statscompleted_desc'] = 'Número mostrado en el stat "Completados" de la página de inicio.';
$string['statsawards'] = 'Stats — logros obtenidos';
$string['statsawards_desc'] = 'Número mostrado en el stat "Logros" de la página de inicio.';
$string['statshours'] = 'Stats — horas de aprendizaje';
$string['statshours_desc'] = 'Número mostrado en el stat "Horas" de la página de inicio.';
$string['viewall'] = 'Ver todo';
$string['searchplaceholder'] = 'Buscar cursos, lecciones…';
$string['markcomplete'] = 'Marcar como completada';
$string['nextlesson'] = 'Siguiente lección';
$string['inthismodule'] = 'En este módulo';

// Etiquetas de navegación de la barra lateral (enlaces a URLs reales de Moodle).
$string['navhome'] = 'Inicio';
$string['navdashboard'] = 'Mi panel';
$string['navmycourses'] = 'Mis cursos';
$string['navcatalogue'] = 'Catálogo';
$string['navcalendar'] = 'Calendario';
$string['navmessages'] = 'Mensajes';

// Nombres de las regiones de bloques.
$string['region-side-pre'] = 'Derecha';
$string['region-side-post'] = 'Izquierda';

// Hero de curso + stats (datos REALES).
$string['herocontinue'] = 'Continuar';
$string['herolessons'] = '{$a} lecciones';
$string['heroenrolled'] = '{$a} inscriptos';
$string['herocompleted'] = '{$a->done} de {$a->total} completadas';
$string['statinprogress'] = 'En progreso';
$string['statcompleted'] = 'Completados';
$string['statawards'] = 'Logros';
$string['boardlabel'] = 'Pizarra de aprendizaje';
$string['boardnote_inprogress'] = '↗ Sigue así ★';
$string['boardnote_completed'] = '¡Buen trabajo!';
$string['boardnote_awards'] = '¡Sigue creciendo!';
$string['boardempty'] = 'Todavía no hay stickies aquí.';

// Hero full-width de la home pública (frontpage).
$string['campusactive'] = 'Campus activo';
$string['welcometocampus'] = '¡bienvenida a tu campus!';
$string['herosubtitle'] = 'Continuá donde lo dejaste y mantené el impulso de aprendizaje.';
$string['featured'] = 'Destacado';

// Página principal (entrada — guests primero; no clona /my/).
$string['frontpage_welcome_guest'] = 'Bienvenido al campus';
$string['frontpage_welcome_back'] = 'Hola, {$a}';
$string['frontpage_subtitle_guest'] = 'Tu espacio para aprender, practicar y crecer.';
$string['frontpage_subtitle_loggedin'] = 'Tu Área personal tiene el muro de stand-up. Seguí desde ahí.';
$string['frontpage_cta_enter'] = 'Entrar';
$string['frontpage_cta_my'] = 'Ir a mi área';

// Login (tablero partido).
$string['brandwordmark'] = 'Aprende';
$string['login_title'] = 'Entrar al campus';
$string['login_subtitle'] = 'Tu espacio para aprender.';

// Hero de bienvenida del panel (datos REALES).
$string['herodashcontinue'] = 'Vas {$a->pct}% en «{$a->course}». ¡Un envión más!';
$string['herodashstart'] = 'Empezá tu próximo curso hoy.';
$string['herodashsubtitle'] = 'Retomá donde lo dejaste y mantené el impulso.';
$string['herodashbrowse'] = 'Explorar cursos';

// Encabezado de la página Mis cursos (conteos reales).
$string['mycoursessubtitle'] = 'Seguís inscripto en {$a->total} cursos · {$a->completed} completados';

// Secciones de landing de la frontpage (catálogo, pasos, valor, CTA).
// Renderizadas por core_course_renderer::aurora_fp_courses_context().
$string['fp_catalog_title'] = 'Cursos disponibles';
$string['fp_catalog_intro'] = 'Explorá los cursos visibles en este campus.';
$string['fp_courses_empty'] = 'Aún no hay cursos disponibles. Volvé pronto.';
$string['fp_viewall'] = 'Ver todos los cursos';

$string['fp_steps_title'] = 'Una ruta clara para avanzar';
$string['fp_step1_title'] = 'Explorar';
$string['fp_step1_desc'] = 'Recorré el catálogo y abrí el curso que se ajusta a tus objetivos.';
$string['fp_step2_title'] = 'Aprender';
$string['fp_step2_desc'] = 'Avanzá por los contenidos y actividades que el curso incluye.';
$string['fp_step3_title'] = 'Continuar';
$string['fp_step3_desc'] = 'Volvé desde tu Área personal o Mis cursos para seguir donde lo dejaste.';

$string['fp_value_title'] = 'Aprendizaje que se queda';
$string['fp_value_body'] = 'Cursos estructurados, contenidos reales y actividades — un campus pensado para aprender a tu ritmo.';
$string['fp_note1_title'] = 'Tu progreso. Tu ritmo.';
$string['fp_note1_body'] = 'Sin presión de ir rápido. Aprendé cuando y como mejor te venga.';
$string['fp_note2_title'] = 'Un campus que guarda tu lugar';
$string['fp_note2_body'] = 'Volvé cuando mejor te venga. Tu progreso se guarda y el curso te espera.';

$string['fp_cta_title'] = '¿Listo para empezar?';
$string['fp_cta_body'] = 'Ingresá para acceder a tus cursos y seguir donde lo dejaste.';
$string['fp_cta_guest'] = 'Entrar a aprender';
$string['fp_cta_loggedin'] = 'Ir a mi área de aprendizaje';

// Privacidad.
$string['privacy:metadata'] = 'El tema Aurora no almacena ningún dato personal.';
