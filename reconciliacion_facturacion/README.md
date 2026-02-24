# Reconciliación de Facturación Sweet ↔ BSale

Aplicación web para comparar y reconciliar la facturación entre SuiteCRM (Sweet) y BSale, detectando discrepancias y permitiendo actualizaciones en ambos sistemas.

## 📋 Características

- **Comparación Automática**: Compara facturas de Sweet con notas de venta de BSale
- **Detección de Discrepancias**: Identifica diferencias en montos, fechas y estados
- **Tipos de Facturación**: Clasifica automáticamente en única, mensual, anual o bienal
- **Conversión de Monedas**: Maneja UF, USD y CLP con conversión automática
- **Filtros Avanzados**: Filtra por tipo de facturación, severidad y búsqueda de texto
- **Exportación CSV**: Exporta resultados para análisis externo
- **Interfaz Moderna**: Diseño premium con tema oscuro y animaciones suaves

## 🚀 Acceso

La aplicación está disponible en:
```
https://intranet.icontel.cl/reconciliacion_facturacion/
```

## 📊 Uso

### 1. Seleccionar Período
- Usa los botones de presets (Mes Actual, Mes Anterior, Año Actual)
- O selecciona fechas personalizadas

### 2. Aplicar Filtros
- **Tipo de Facturación**: Única, Mensual, Anual, Bienal
- **Estado**: Sin problemas, Advertencias, Errores
- **Búsqueda**: Por cliente, RUT o número de NV

### 3. Analizar Resultados
- **Dashboard de Estadísticas**: Resumen visual de problemas
- **Resumen por Tipo**: Totales por tipo de facturación
- **Tabla Detallada**: Comparación línea por línea

### 4. Exportar Datos
- Haz clic en "Exportar CSV" para descargar los resultados

## 🔧 Estructura de Archivos

```
reconciliacion_facturacion/
├── index.php                 # Interfaz principal
├── ajax_handler.php          # Manejador de peticiones AJAX
├── includes/
│   ├── sb_config.php         # Configuración y funciones base
│   ├── api_bsale.php         # API de BSale
│   ├── query_invoices.php    # Consultas SQL
│   └── reconciliation_engine.php  # Motor de reconciliación
├── css/
│   └── reconciliacion.css    # Estilos
└── js/
    └── reconciliacion.js     # Lógica frontend
```

## 🎨 Tipos de Discrepancias

### ✅ Sin Problemas (Verde)
- Factura existe en ambos sistemas
- Montos coinciden (tolerancia 1%)
- Fechas consistentes

### ⚠️ Advertencias (Amarillo)
- Diferencia de monto entre 1% y 5%
- Diferencia de fecha mayor a 7 días

### ❌ Errores (Rojo)
- Factura no existe en BSale
- Diferencia de monto mayor a 5%
- Documento anulado en BSale

## 💡 Detección de Tipo de Facturación

El sistema detecta automáticamente el tipo basándose en el nombre de la factura:

- **Única**: Sin patrón de recurrencia
- **Mensual**: Contiene "mensual" o "monthly"
- **Anual**: Contiene "anual", "annual" o "yearly"
- **Bienal**: Contiene "bienal", "biennial" o "2 años"

## 🔐 Seguridad

- Requiere sesión activa de KickOff
- Validación de permisos de usuario
- Registro de auditoría de cambios (si está habilitado)

## 📝 Configuración

### Variables de Configuración (includes/sb_config.php)

```php
define('BSALE_TOKEN', '...');           // Token API BSale
define('REQUIRE_CONFIRMATION', true);   // Confirmar antes de actualizar
define('ENABLE_AUDIT_LOG', true);       // Habilitar log de auditoría
define('CURRENCY_TOLERANCE', 0.01);     // Tolerancia 1%
```

## 🔄 Conversión de Monedas

El sistema obtiene valores actuales de UF y USD desde la base de datos Sweet y convierte todo a CLP para comparación:

- **UF**: Valor diario desde `moneda_ultimo_valor(6)`
- **USD**: Valor diario desde `moneda_ultimo_valor(2)`
- **CLP**: Sin conversión

## 📊 Query Principal

La aplicación ejecuta la siguiente consulta SQL:

```sql
SELECT
    ai.id,
    ai.number AS fac_numero,
    aic.num_nota_venta1_c AS nv_numero,
    ai.name AS fac_nombre,
    ai.invoice_date AS fac_fecha,
    ai.quote_number AS coti_numero,
    ai.subtotal_amount AS total_neto,
    ai.total_amt,
    ai.total_amount,
    CASE 
        WHEN ai.currency_id = '-99' THEN 'UF'
        ELSE cu.symbol
    END AS fac_moneda,
    CONCAT_WS(' ', us.first_name, us.last_name) AS fac_asignado,
    CONCAT(
        'https://sweet.icontel.cl/index.php?module=AOS_Invoices&action=DetailView&record=',
        ai.id
    ) AS url_fac,
    cbd.id_bsale,
    cbd.tipo_doc,
    cbd.num_doc,
    cbd.fecha_emision,
    cbd.fecha_vencimiento,
    cbd.razon_social,
    cbd.rut AS rut_cliente,
    cbd.direccion,
    cbd.comuna,
    cbd.ciudad,
    cbd.id_moneda,
    cbd.valor_uf,
    cbd.total_uf,
    cbd.neto_uf,
    cbd.iva_uf,
    cbd.netAmount,
    cbd.totalAmount AS total_pesos,
    cbd.urlPdf,
    cbd.urlPublicView,
    cbd.state AS estado
FROM aos_invoices ai
JOIN aos_invoices_cstm aic 
     ON aic.id_c = ai.id
LEFT JOIN aos_quotes aq 
       ON aq.number = ai.quote_number
LEFT JOIN users us 
       ON us.id = ai.assigned_user_id
LEFT JOIN currencies cu 
       ON cu.id = ai.currency_id
LEFT JOIN icontel_clientes.cron_bsale_documents cbd
       ON cbd.num_doc = aic.num_nota_venta1_c
      AND cbd.tipo_doc = 'NOTA DE VENTA'
WHERE ai.deleted = 0
  AND aic.num_nota_venta1_c < 900000000
  AND (
        ai.status LIKE '%FACTURADO%'
        AND ai.invoice_date BETWEEN ? AND ?
     OR (
        ai.status LIKE '%VIGENTE%'
        AND aq.stage = 'Closed Accepted'
     )
  )
ORDER BY cbd.fecha_emision DESC
```

## 🛠️ Mantenimiento

### Actualizar Token de BSale
Editar `includes/sb_config.php` y actualizar:
```php
define('BSALE_TOKEN', 'nuevo_token_aqui');
```

### Habilitar/Deshabilitar Audit Log
```php
define('ENABLE_AUDIT_LOG', true);  // true o false
```

## 📞 Soporte

Para problemas o consultas, contactar al equipo de desarrollo de iConTel.

---

**Autor**: Mauricio Araneda (mAo)  
**Fecha**: Diciembre 2025  
**Versión**: 1.0
