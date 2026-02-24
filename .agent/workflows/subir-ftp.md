---
description: Subir archivos al servidor FTP de intranet
---

# Workflow: Subir archivos a producción vía FTP

## Conexión FTP
- Host: ftp.icontel.cl
- Usuario: icontel
- Ruta remota base: public_html/intranet/
- Ruta local base: /Users/octavioaranedaojeda/Documents/Diseño/Clientes/iConTel/intranet/

## Uso

Para subir un archivo, indica la **ruta relativa** desde `intranet/`. Por ejemplo:
- `kickoff_ajax/cm_cobranza_comercial.php`
- `kickoff_ajax/css/cm_cobranza_comercial.css`
- `duemint/tabla.php`

## Comando genérico para subir un archivo

// turbo-all

Reemplaza `[RUTA_RELATIVA]` con la ruta del archivo a subir:

```bash
curl -T /Users/octavioaranedaojeda/Documents/Diseño/Clientes/iConTel/intranet/[RUTA_RELATIVA] --user 'icontel:q_#(0R06MzEx' ftp://ftp.icontel.cl/public_html/intranet/[RUTA_RELATIVA]
```

## Ejemplos frecuentes

### Subir archivo específico de kickoff_ajax
```bash
# Ejemplo: subir cm_cobranza_comercial.php
curl -T /Users/octavioaranedaojeda/Documents/Diseño/Clientes/iConTel/intranet/kickoff_ajax/cm_cobranza_comercial.php --user 'icontel:q_#(0R06MzEx' ftp://ftp.icontel.cl/public_html/intranet/kickoff_ajax/cm_cobranza_comercial.php && echo "✅ Subido"
```

### Subir archivo de CSS
```bash
curl -T /Users/octavioaranedaojeda/Documents/Diseño/Clientes/iConTel/intranet/kickoff_ajax/css/[ARCHIVO].css --user 'icontel:q_#(0R06MzEx' ftp://ftp.icontel.cl/public_html/intranet/kickoff_ajax/css/[ARCHIVO].css && echo "✅ Subido"
```

### Subir archivo de JS
```bash
curl -T /Users/octavioaranedaojeda/Documents/Diseño/Clientes/iConTel/intranet/kickoff_ajax/js/[ARCHIVO].js --user 'icontel:q_#(0R06MzEx' ftp://ftp.icontel.cl/public_html/intranet/kickoff_ajax/js/[ARCHIVO].js && echo "✅ Subido"
```

### Subir archivo de includes
```bash
curl -T /Users/octavioaranedaojeda/Documents/Diseño/Clientes/iConTel/intranet/kickoff_ajax/includes/[ARCHIVO].php --user 'icontel:q_#(0R06MzEx' ftp://ftp.icontel.cl/public_html/intranet/kickoff_ajax/includes/[ARCHIVO].php && echo "✅ Subido"
```

## Módulos completos

### Subir todos los archivos de Cobranza Comercial
```bash
curl -T /Users/octavioaranedaojeda/Documents/Diseño/Clientes/iConTel/intranet/kickoff_ajax/cm_cobranza_comercial.php --user 'icontel:q_#(0R06MzEx' ftp://ftp.icontel.cl/public_html/intranet/kickoff_ajax/cm_cobranza_comercial.php && \
curl -T /Users/octavioaranedaojeda/Documents/Diseño/Clientes/iConTel/intranet/kickoff_ajax/includes/cm_cobranza_comercia_include.php --user 'icontel:q_#(0R06MzEx' ftp://ftp.icontel.cl/public_html/intranet/kickoff_ajax/includes/cm_cobranza_comercia_include.php && \
curl -T /Users/octavioaranedaojeda/Documents/Diseño/Clientes/iConTel/intranet/kickoff_ajax/css/cm_cobranza_comercial.css --user 'icontel:q_#(0R06MzEx' ftp://ftp.icontel.cl/public_html/intranet/kickoff_ajax/css/cm_cobranza_comercial.css && \
curl -T /Users/octavioaranedaojeda/Documents/Diseño/Clientes/iConTel/intranet/kickoff_ajax/js/cm_cobranza_comercial_v2.js --user 'icontel:q_#(0R06MzEx' ftp://ftp.icontel.cl/public_html/intranet/kickoff_ajax/js/cm_cobranza_comercial_v2.js && \
echo "✅ Cobranza Comercial actualizado"
```
