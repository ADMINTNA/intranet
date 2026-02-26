# WHM Server Report - Icontel Intranet

## Instalación

### 1. Crear API Token en WHM
1. Ingresa a WHM como root: `https://tu-servidor:2087`
2. Ve a **Development → Manage API Tokens**
3. Click **Generate Token**
4. Nombre: `intranet-whm-report`
5. Permisos: **Everything** (o los específicos listados abajo)
6. Copia y guarda el token

### 2. Subir archivos
Copia toda la carpeta `whm/` a:
```
/home/icontel/public_html/intranet/whm/
```

### 3. Configurar
Edita el archivo `config/config.php`:
```php
define('WHM_HOST', 'tu-servidor.com');     // IP o dominio WHM
define('WHM_PORT', 2087);                   // Puerto
define('WHM_USERNAME', 'root');             // Usuario
define('WHM_API_TOKEN', 'TU_TOKEN_AQUI');  // API Token
```

### 4. Acceder
Abre: `https://intranet.icontel.cl/whm/`

## Estructura
```
whm/
├── index.php              # Dashboard principal
├── .htaccess              # Seguridad
├── config/
│   ├── config.php         # Configuración WHM API
│   └── .htaccess          # Bloquea acceso directo
├── includes/
│   └── WhmApi.php         # Clase cliente API
├── api/
│   └── index.php          # Endpoint JSON
└── assets/
    ├── css/style.css      # Estilos
    └── js/app.js          # Frontend JS
```

## Permisos API mínimos requeridos
- list-accts
- accountsummary  
- showbw
- list-pops (para detalle email)
- listdbs (para detalle bases de datos)
- cpanel (para UAPI calls)
- gethostname
- version
- listsuspended

## Endpoints API internos
- `api/?action=test` — Test de conexión
- `api/?action=report` — Reporte completo
- `api/?action=account_detail&user=xxx` — Detalle de cuenta
- `api/?action=server_info` — Info del servidor
- `api/?action=bandwidth` — Datos de bandwidth
