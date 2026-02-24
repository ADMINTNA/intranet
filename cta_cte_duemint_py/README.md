# Cuenta Corriente Duemint - Versión Python/Flask

Módulo de búsqueda de cuenta corriente en Duemint por RUT, implementado en Python con Flask.

## Características

- Backend en Python con Flask
- Conexión a MySQL usando PyMySQL
- API REST para búsqueda por RUT
- Interfaz web moderna con tema claro/oscuro
- Auto-formato de RUT en tiempo real
- Búsqueda AJAX sin recargar página

## Estructura del Proyecto

```
cta_cte_duemint_py/
├── app.py              # Aplicación Flask principal
├── config.py           # Configuración de BD y lógica de búsqueda
├── requirements.txt    # Dependencias Python
├── templates/
│   └── index.html     # Template HTML con Jinja2
└── static/
    └── styles.css     # Estilos CSS
```

## Instalación

### 1. Crear entorno virtual

```bash
cd /Users/octavioaranedaojeda/Documents/Diseño/Clientes/iConTel/intranet/cta_cte_duemint_py
python3 -m venv venv
source venv/bin/activate  # En Windows: venv\Scripts\activate
```

### 2. Instalar dependencias

```bash
pip install -r requirements.txt
```

### 3. Configurar base de datos

Editar `config.py` si es necesario para ajustar credenciales:

```python
DB_CONFIG = {
    'host': 'localhost',
    'user': 'data_studio',
    'password': '1Ngr3s0.,',
    'database': 'tnaoffice_clientes',
    'charset': 'utf8mb4'
}
```

## Uso

### Modo Desarrollo

```bash
python app.py
```

La aplicación estará disponible en: `http://localhost:5000`

### Modo Producción

Para producción, usar un servidor WSGI como Gunicorn:

```bash
pip install gunicorn
gunicorn -w 4 -b 0.0.0.0:5000 app:app
```

## API Endpoints

### GET /
Página principal con formulario de búsqueda

### POST /api/buscar
Buscar cuenta corriente por RUT

**Request:**
```json
{
  "rut": "12.345.678-9"
}
```

**Response (encontrado):**
```json
{
  "success": true,
  "encontrado": true,
  "rut": "123456789",
  "datos": {
    "pagada": 0,
    "por_vencer": 150000,
    "vencida": 50000,
    "portal_url": "https://cliente.duemint.com/..."
  }
}
```

**Response (no encontrado):**
```json
{
  "success": true,
  "encontrado": false,
  "rut": "123456789",
  "datos": {
    "pagada": 0,
    "por_vencer": 0,
    "vencida": 0,
    "portal_url": "https://www.duemint.com"
  }
}
```

### GET /health
Health check endpoint

**Response:**
```json
{
  "status": "ok"
}
```

## Diferencias con la Versión PHP

### Ventajas de Python/Flask

1. **Tipado y validación**: Uso de type hints para mejor documentación
2. **Manejo de errores**: Try/except más robusto
3. **Estructura modular**: Separación clara entre lógica y presentación
4. **Testing**: Más fácil escribir tests unitarios
5. **Async**: Posibilidad de usar async/await para mejor performance

### Funcionalidad Equivalente

- Misma base de datos y stored procedure
- Misma interfaz de usuario
- Mismos resultados de búsqueda
- API REST en lugar de PHP directo

## Desarrollo

### Ejecutar en modo debug

```bash
export FLASK_ENV=development
python app.py
```

### Estructura de código

- `config.py`: Contiene toda la lógica de base de datos
- `app.py`: Define rutas y endpoints de Flask
- `templates/`: Templates HTML con Jinja2
- `static/`: Archivos estáticos (CSS, JS, imágenes)

## Despliegue

Para desplegar en producción, considerar:

1. **Servidor WSGI**: Gunicorn o uWSGI
2. **Reverse Proxy**: Nginx o Apache
3. **Variables de entorno**: Para credenciales sensibles
4. **Logs**: Configurar logging apropiado
5. **Monitoreo**: Health checks y métricas

## Notas

- El código PHP original permanece intacto en `cta_cte_duemint/`
- Esta versión Python es completamente independiente
- Ambas versiones usan la misma base de datos
- Ambas versiones tienen la misma funcionalidad
