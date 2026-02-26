# ─────────────────────────────────────────────────────────────
# zabbix_mao/.htaccess
# Configuración de variables de entorno para cPanel/WHM
#
# INSTRUCCIONES:
#   1. Editar los valores de SetEnv con las credenciales reales
#   2. Este archivo NO debe ser versionado en Git (agregar a .gitignore)
#   3. En cPanel: subir a public_html/zabbix_mao/ (o donde esté el módulo)
#   4. Verificar que mod_env esté habilitado (en cPanel siempre lo está)
# ─────────────────────────────────────────────────────────────

# ── Credenciales Zabbix API ───────────────────────────────────
SetEnv ZABBIX_URL  "https://zabbix.tnasolutions.cl/zabbix/api_jsonrpc.php"
SetEnv ZABBIX_USER "Admin"
SetEnv ZABBIX_PASS "CAMBIAR_POR_NUEVA_CONTRASENA"

# ── Credenciales Base de Datos local ─────────────────────────
SetEnv DB_HOST "localhost"
SetEnv DB_USER "tnasolut_app"
SetEnv DB_PASS "CAMBIAR_POR_NUEVA_CONTRASENA"
SetEnv DB_NAME "tnasolut_app"

# ── Configuración de la aplicación ───────────────────────────
SetEnv ALLOWED_ORIGIN "https://intranet.icontel.cl"

# ─────────────────────────────────────────────────────────────
# Bloquea acceso web a archivos de debug, benchmark, migración
# y al propio config.php (solo deben ejecutarse desde PHP interno)
# ─────────────────────────────────────────────────────────────
<FilesMatch "^(debug_|benchmark|backfill|db_update|zabbix_acks_logs_backfill|config)">
    Require all denied
</FilesMatch>
