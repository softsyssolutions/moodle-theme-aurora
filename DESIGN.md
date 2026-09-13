---
version: alpha
name: Aurora Whiteboard
description: >-
  Pizarra de stand-up para campuses IOMAD/Moodle (Aprende y clones).
  Mundo visual de sticky notes, marcador y tipografía Jost + Nanum —
  nunca purple SaaS ni cards LMS genéricas.
colors:
  primary: "#188838"
  secondary: "#a2d984"
  tertiary: "#f8db53"
  ink: "#0f172a"
  on-primary: "#ffffff"
  background: "#f4f5f5"
  surface: "#ffffff"
  board: "#f8fafc"
  sticky-green: "#a2d984"
  sticky-yellow: "#f8db53"
  success: "#188838"
  warning: "#f59e0b"
  muted: "#4b5563"
  border: "#c5c5c7"
  pin: "#c0392b"
  # SoftSys AI H5P/SCORM aliases (parser reads brand|primary, bg|background, ink|on-surface)
  # bg/ink/surface already match the parser's own key names above — no duplicate needed.
  brand: "#188838"       # = primary
  brand-2: "#a2d984"     # = secondary
  ok: "#188838"          # = success
  warn: "#f59e0b"        # = warning
typography:
  display-hand:
    fontFamily: "Nanum Brush Script"
    fontSize: 2rem
    fontWeight: 400
    lineHeight: 1.1
  note-hand:
    fontFamily: "Nanum Pen Script"
    fontSize: 1.5rem
    fontWeight: 400
    lineHeight: 1.15
  h1:
    fontFamily: "Jost"
    fontSize: 1.75rem
    fontWeight: 600
    lineHeight: 1.2
    letterSpacing: -0.01em
  h2:
    fontFamily: "Jost"
    fontSize: 1.25rem
    fontWeight: 600
    lineHeight: 1.3
  body:
    fontFamily: "Jost"
    fontSize: 1rem
    fontWeight: 400
    lineHeight: 1.5
  label:
    fontFamily: "Jost"
    fontSize: 0.75rem
    fontWeight: 500
    letterSpacing: 0.01em
  lane-title:
    fontFamily: "Nanum Brush Script"
    fontSize: 1.75rem
    fontWeight: 400
    lineHeight: 1
rounded:
  none: 0px
  sm: 2px
  md: 4px
  pill: 9999px
spacing:
  xs: 4px
  sm: 8px
  md: 16px
  lg: 24px
  xl: 32px
  xxl: 48px
components:
  button-primary:
    backgroundColor: "{colors.primary}"
    textColor: "{colors.on-primary}"
    rounded: "{rounded.pill}"
    padding: "10px 20px"
  sticky-continue:
    backgroundColor: "{colors.sticky-yellow}"
    textColor: "{colors.ink}"
    rounded: "{rounded.md}"
  sticky-course:
    backgroundColor: "{colors.sticky-green}"
    textColor: "{colors.ink}"
    rounded: "{rounded.md}"
  sticky-award:
    backgroundColor: "{colors.sticky-yellow}"
    textColor: "{colors.ink}"
    rounded: "{rounded.md}"
  lane-underline:
    backgroundColor: "{colors.board}"
    textColor: "{colors.ink}"
  board-chrome:
    backgroundColor: "{colors.board}"
    textColor: "{colors.ink}"
  pin-dot:
    backgroundColor: "{colors.pin}"
    textColor: "{colors.on-primary}"
  meta-muted:
    backgroundColor: "{colors.surface}"
    textColor: "{colors.muted}"
  warning-chip:
    backgroundColor: "{colors.warning}"
    textColor: "{colors.ink}"
  border-hairline:
    backgroundColor: "{colors.border}"
    textColor: "{colors.ink}"
---

## Overview

**Aurora Whiteboard** es la identidad visual del theme Moodle `theme_aurora`
cuando vive el mundo *Pizarra de stand-up* (comp aprobado
`comp-whiteboard-2` / template `safer-whiteboard`).

Emoción: campus adulto cercano, hecho a mano, listo para retomar el trabajo —
no un LMS corporativo púrpura ni un dashboard de métricas SaaS.

Personalidad: marcador permanente + sticky notes + tipografía mixta
(Jost geométrica + Nanum Brush/Pen manuscrita). El hero de `/my/` es el
sticky **Continuar**; las lanes En progreso / Completados / Logros son
columnas del muro; Mis cursos es un rail horizontal de stickies verdes.

Audiencia: aprendices de company campus (IOMAD). Datos siempre reales de
Moodle — nunca HTML inventado de cursos o porcentajes fake.

Fuente de tokens en código: `scss/post-legacy.scss` (`:root` `--aurora-*`).
Este archivo es la fuente de verdad para agentes; el SCSS debe mantenerse
alineado.

## Colors

Paleta corta, de marcador sobre pizarra:

- **Primary / success (`#188838`)**: tinta verde de CTA, underlines de lanes
  y wordmark Aprende. Un solo verde “activo”.
- **Sticky green (`#a2d984`)**: papel de stickies de curso (en progreso,
  completados, Mis cursos).
- **Sticky yellow / tertiary (`#f8db53`)**: sticky Continuar, insignias,
  título “Mis cursos” manuscrito.
- **Ink (`#0f172a`)**: texto, barras de progreso, bordes de marcador.
- **Board (`#f8fafc`) + background (`#f4f5f5`)**: lienzo con grid sutil —
  no crema vintage ni púrpura.
- **Pin (`#c0392b`)**: chincheta decorativa (un punto, no un sistema de alerta).

No inventar acentos (morado, lilac, glow neón, gradients “AI purple”).
Si hace falta un tercer sticky, preferir blanco/crema de papel, no un
nuevo brand color.

## Typography

Dos familias, roles fijos:

- **Jost** — UI, títulos de curso, % , nav, botones. Geométrica, legible.
- **Nanum Brush Script / Nanum Pen Script** — “Continuar”, lanes, notas del
  muro (“Sigue así”), wordmark cuando el logo de company oculta el sitename.

No mezclar Inter/Roboto/system como display. No usar script en párrafos
largos ni en formularios.

## Layout

- Primer viewport de `/my/`: nav + sticky Continuar + lanes; debajo tray
  decorativo; luego Mis cursos (rail).
- Espaciado en escala 4/8/16/24/32 — no inventar valores intermedios sin
  necesidad.
- Grid de pizarra en `#topofscroll` (mydashboard). Contenido Moodle real
  vía `main_content` intacto; el board se inyecta en `full_header` solo
  en `mydashboard`.
- Rail Mis cursos: horizontal, `nowrap`, scroll-x; cards ~200px.

## Elevation & Depth

Profundidad de papelería, no de neumorfismo:

- Stickies: sombra corta dura (`2px 6px 14px` ink/14%) + rotación leve
  (−0.8° a −2°).
- Sin multi-layer glow. Sin glassmorphism.
- Tray inferior: sugerencia de repisa física (CSS), no foto stock.

## Shapes

- Stickies y superficies de board: radio **2–4px** (casi recto, como papel).
- CTAs: **pill** (`9999px`).
- Contadores de lane: círculo marker con borde ink grueso.
- Evitar `rounded-2xl` / cards “app store”.

## Components

### Sticky Continuar
Fondo yellow, tipografía Nanum en el label, Jost en curso/%, CTA verde pill
con “Continuar →”. Es el único hero.

### Sticky de curso (verde)
Título + progreso o check. Sin foto full-bleed estilo marketplace; si hay
cover, es un doodle/iconografía whiteboard, no foto stock de oficina.

### Sticky de logro (amarillo)
Icono de insignia 36–48px + nombre + descripción corta. El icono debe
leerse como dibujo de marcador (ver Asset generation).

### Lane titles
Nanum verde + underline inset verde + círculo contador ink.

### Course overview rail
Mismos stickies verdes; pin rojo en el primero; chevron de scroll opcional.

### Iconografía / covers
Ver sección **Asset generation** (después de Do's). Regla: todo raster
nuevo debe poder colgarse en el muro sin romper la metáfora de pizarra.

## Do's and Don'ts

**Do**

- Leer este `DESIGN.md` antes de generar UI, SCSS, iconos o covers.
- Usar tokens hex de arriba; referenciar `{colors.*}` al exportar.
- Mantener EN/ES vía lang strings; no hardcodear copy de producto en SCSS
  salvo relabel visual controlado (ej. “Mis cursos”).
- Preferir datos Moodle (`enrol_get_my_courses`, completion, badges).
- Generar assets con el prompt maestro de Asset generation + referencia
  visual `comp-whiteboard-2`.

**Don't**

- No reintroducir morado / lilac / gradients “Aurora SaaS” legacy.
- No inventar cursos, % o insignias en HTML/Mustache.
- No usar fotos stock realistas (laptops, handshakes stock) como cover
  principal — rompen la pizarra.
- No poner cards con sombra suave grande, border-radius > 8px en stickies,
  ni tipografía script en body.
- No alterar layouts Boost core (`#page`, drawers) — solo piel.

## Asset generation

> Sección de extensión (fuera del orden canónico de tokens). Los agentes
> deben leerla al crear **insignias, covers de curso, iconos tray, empty
> states o ilustraciones** para este theme.

### Prompt maestro (copiar y especializar)

```text
Style: hand-drawn whiteboard / sticky-note iconography for the Aurora
Whiteboard Moodle theme. Flat matte paper, subtle paper grain OK.
Palette ONLY: sticky yellow #f8db53, sticky green #a2d984, marker green
#188838, ink #0f172a, paper #f8fafc, optional pin red #c0392b.
Line work: thick uneven black marker strokes, slight wobble, not vector-perfect.
NO photorealism, NO 3D render, NO purple/lilac, NO glassmorphism, NO
gradients that look like SaaS dashboards, NO stock photography, NO busy
backgrounds. Centered subject, generous margin, readable at 36–100px.
Typography inside art (if any): simple sans or marker lettering; Spanish OK.
Mood: adult learner stand-up board, friendly, craft — not childish cartoon.
Reference world: physical whiteboard with sticky notes and Expo markers.
```

### Insignias (badges)

- Lienzo cuadrado 512×512 (Moodle generará f1/f2/f3).
- Fondo sticky yellow `#f8db53` o círculo de papel sobre transparente.
- Motivo central: un solo pictograma de marcador (estrella, check en
  círculo, megáfono, rejilla Excel, burbujas de diálogo, moneda simple).
- Sin texto largo; nombre de badge vive en el sticky HTML, no en el PNG
  (salvo monograma de 1–2 letras si aporta).
- Export PNG con alpha opcional; Moodle prefiere opaco.

Especialización ejemplo:

```text
{PROMPT MAESTRO}
Subject: circular badge sticker for "Productividad Pro" — hand-drawn
checkmark inside a rough circle, small star accent, sticky-yellow field.
```

### Covers de curso (overviewfiles)

- 800×450 o 1200×675 (16:9), JPEG/PNG.
- Escena abstracta de pizarra o gran sticky verde/amarillo con un doodle
  del tema del curso (planilla, megáfono, checklist, handshake sketch).
- Debe funcionar recortada detrás de tipografía oscura; evitar detalle fino.
- No logos de marcas reales (Microsoft, etc.).

Especialización ejemplo:

```text
{PROMPT MAESTRO}
Subject: course cover for "Excel desde cero" — large green sticky note on
graph-paper board, hand-drawn spreadsheet grid and a bold ink checkmark.
```

### Provenance

Guardar rasters generados bajo `pix/whiteboard/` con nombre estable
(`badge-<slug>.png`, `course-<shortname>.jpg`) y aplicarlos con:

```bash
php theme/aurora/cli/seed_whiteboard_assets.php
# o dry-run:
php theme/aurora/cli/seed_whiteboard_assets.php --dry-run
```

Preferir covers JPEG ≤ ~250KB y badges PNG ≤ ~400KB (512×512).

### Anti-referencias

No usar: Corporate Memphis, isometric 3D, neon cyber, purple AI gradients,
photoreal office, iOS emoji dumps, busy collages.
