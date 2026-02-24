# KickOff iConTel - Versión de Desarrollo

Esta es una copia de desarrollo de `kickoff_icontel` creada el 2026-02-17.

## ⚠️ IMPORTANTE

Esta versión incluye **bypass temporal de autenticación** para facilitar el desarrollo y pruebas.

### Archivos con bypass:
- `icontel.php` (líneas 11-16)
- `ajax_bootstrap.php` (líneas 26-32)
- `cambiar_grupo.php` (líneas 8-12)

### Usuario de prueba:
- **Usuario**: Mauricio
- **Grupo**: Soporte técnico
- **ID Grupo**: a03a40e8-bda8-0f1b-b447-58dcfb6f5c19

## 🔒 Para producción

**ANTES de subir a producción**, debes:

1. Eliminar todos los bypass temporales
2. Configurar autenticación correcta
3. Los usuarios deben hacer login en `https://intranet.icontel.cl/` primero

## 📁 Estructura

- `kickoff_icontel/` - Versión estable (NO TOCAR)
- `kickoff_icontel_dev/` - Versión de desarrollo (esta carpeta)

## 🚀 Uso

Accede a: `https://intranet.icontel.cl/kickoff_icontel_dev/icontel.php`
