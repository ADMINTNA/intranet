# Estructura de Archivos - Integración SuiteCRM

## 📁 Archivos Creados

### 1. Módulo de Verificación de Sesión
```
📄 /intranet/includes/sweet_session_check.php
```
**Descripción**: Clase PHP con métodos para verificar sesión de SuiteCRM y extraer datos del usuario.

**Funciones**:
- `SweetSessionCheck::isLoggedIn()` - Verifica sesión activa
- `SweetSessionCheck::getUserId()` - Obtiene user_id
- `SweetSessionCheck::getUserData()` - Obtiene datos del usuario
- `SweetSessionCheck::getSecurityGroups($user_id)` - Obtiene security groups

---

### 2. Script de Diagnóstico
```
📄 /intranet/test_sweet_session.php
```
**Descripción**: Herramienta visual para diagnosticar el estado de la sesión de SuiteCRM.

**URL de acceso**: `https://intranet.icontel.cl/test_sweet_session.php`

**Muestra**:
- Estado de sesión (activa/inactiva)
- Datos del usuario autenticado
- Security groups asignados
- Variables de sesión completas

---

### 3. Kickoff con Autenticación SuiteCRM
```
📄 /intranet/kickoff_ajax/icontel_sweet.php
```
**Descripción**: Nueva versión de Kickoff que requiere sesión activa de SuiteCRM.

**URL de acceso**: `https://intranet.icontel.cl/kickoff_ajax/icontel_sweet.php`

**Características**:
- Verifica sesión de SuiteCRM al inicio
- Redirige a Sweet si no hay sesión
- Extrae security groups automáticamente
- Compatible con sistema actual

---

## 📂 Estructura de Directorios

```
/intranet/
├── includes/
│   └── sweet_session_check.php          ← Módulo de verificación
│
├── kickoff_ajax/
│   ├── icontel.php                      ← Original (sin cambios)
│   └── icontel_sweet.php                ← Nueva versión con SuiteCRM
│
└── test_sweet_session.php               ← Script de diagnóstico
```

---

## 🔄 Archivos Relacionados (No Modificados)

### Archivos del Sistema Actual
```
📄 /intranet/index.php                   ← Login tradicional (sin cambios)
📄 /intranet/kickoff_ajax/icontel.php    ← Kickoff original (sin cambios)
📄 /intranet/kickoff_ajax/config.php     ← Configuración (sin cambios)
📄 /intranet/kickoff_ajax/security_groups.php ← Grupos (sin cambios)
```

### Base de Datos
```
🗄️ tnasolut_sweet                        ← Base de datos de SuiteCRM
   ├── users                             ← Tabla de usuarios
   ├── securitygroups                    ← Tabla de grupos
   └── securitygroups_users              ← Relación usuarios-grupos
```

---

## 🚀 Instalación

### Paso 1: Verificar Archivos Locales
Los archivos ya están creados en tu repositorio local:
- ✅ `/Users/octavioaranedaojeda/Documents/Diseño/Clientes/iConTel/intranet/includes/sweet_session_check.php`
- ✅ `/Users/octavioaranedaojeda/Documents/Diseño/Clientes/iConTel/intranet/test_sweet_session.php`
- ✅ `/Users/octavioaranedaojeda/Documents/Diseño/Clientes/iConTel/intranet/kickoff_ajax/icontel_sweet.php`

### Paso 2: Subir al Servidor
Subir los archivos manteniendo la misma estructura de directorios:
```bash
# Desde tu repositorio local, subir vía FTP/SFTP o Git
/intranet/includes/sweet_session_check.php
/intranet/test_sweet_session.php
/intranet/kickoff_ajax/icontel_sweet.php
```

### Paso 3: Verificar Permisos
```bash
chmod 644 /intranet/includes/sweet_session_check.php
chmod 644 /intranet/test_sweet_session.php
chmod 644 /intranet/kickoff_ajax/icontel_sweet.php
```

### Paso 4: Probar
1. Acceder a `https://intranet.icontel.cl/test_sweet_session.php`
2. Verificar que detecta la sesión de SuiteCRM
3. Probar `https://intranet.icontel.cl/kickoff_ajax/icontel_sweet.php`

---

## 📝 Notas Importantes

### Rutas Absolutas en el Código
Todos los archivos incluyen la ruta completa en el comentario inicial:
```php
/**
 * =============================================================================
 * ARCHIVO: /intranet/includes/sweet_session_check.php
 * =============================================================================
 */
```

### Rutas Relativas en Includes
Los archivos usan rutas relativas para los `require_once`:
```php
// En test_sweet_session.php
require_once('includes/sweet_session_check.php');

// En icontel_sweet.php
require_once('../includes/sweet_session_check.php');
```

### No Modificar Producción
Los archivos actuales **NO se modifican**:
- ❌ `index.php` - Sin cambios
- ❌ `kickoff_ajax/icontel.php` - Sin cambios
- ✅ Se crean archivos nuevos en paralelo

---

## 🎯 Siguiente Paso

Probar en el servidor:
```
https://intranet.icontel.cl/test_sweet_session.php
```

Este script te dirá si la sesión de SuiteCRM se está compartiendo correctamente.
