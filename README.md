# 3 Orillas — sitio web

Tema y plugin de WordPress del sitio de [3 Orillas](https://3orillas.com), estudio digital de Bogotá.

- `app/public/wp-content/themes/tresorillas/` — tema híbrido: `theme.json` con los tokens de marca y plantillas PHP. Movimiento con GSAP (servido desde el tema).
- `app/public/wp-content/plugins/tresorillas-core/` — tipo de contenido Proyecto, servicios, formularios de contacto (guardan en WordPress y avisan por correo), ajustes y comando `wp tor sembrar`.
- `CLAUDE.md` — reglas, decisiones y estado del proyecto.

Desarrollo local con LocalWP. Carga inicial del contenido:

```
wp tor sembrar --media="<ruta>/media"
```
