"""
Configuración de base de datos para consulta Duemint
"""
import pymysql
from typing import Optional, Dict, Any

# Configuración de la base de datos
DB_CONFIG = {
    'host': 'california.icontel.cl',  # Configurado según indicación del usuario
    'user': 'data_studio',
    'password': '1Ngr3s0.,',
    'database': 'tnaoffice_clientes',
    'charset': 'utf8mb4',
    'cursorclass': pymysql.cursors.DictCursor
}


def get_db_connection():
    """
    Crea y retorna una conexión a la base de datos
    """
    try:
        connection = pymysql.connect(**DB_CONFIG)
        return connection
    except pymysql.Error as e:
        print(f"Error conectando a la base de datos: {e}")
        raise


def limpiar_rut(rut: str) -> str:
    """
    Limpia y formatea un RUT removiendo puntos y espacios (mantiene el guión)
    
    Args:
        rut: RUT a limpiar
        
    Returns:
        RUT limpio sin puntos ni espacios
    """
    if not rut:
        return ""
    
    # Remover espacios y puntos (Mantener el guión como en la versión PHP)
    return rut.replace(' ', '').replace('.', '')


def format_currency(value: float) -> str:
    """
    Formatea un número como moneda chilena
    
    Args:
        value: Valor a formatear
        
    Returns:
        Valor formateado como string
    """
    return f"${value:,.0f}".replace(',', '.')


def buscar_cuenta_duemint(rut: str) -> Dict[str, Any]:
    """
    Busca información de cuenta corriente en Duemint por RUT
    
    Args:
        rut: RUT del cliente a buscar
        
    Returns:
        Diccionario con la información de la cuenta
    """
    rut_limpio = limpiar_rut(rut)
    
    if not rut_limpio:
        return {
            'success': False,
            'error': 'RUT no proporcionado'
        }
    
    connection = None
    try:
        connection = get_db_connection()
        
        with connection.cursor() as cursor:
            # Usar execute directo como en PHP para evitar problemas con callproc
            # y escape manual del RUT para seguridad
            rut_escrita = connection.escape_string(rut_limpio)
            sql = f"CALL tnaoffice_clientes.searchbyrut('{rut_escrita}')"
            
            cursor.execute(sql)
            results = cursor.fetchall()
            
            # Inicializar valores
            dumit_pagada = 0
            dumit_por_vencer = 0
            dumit_vencida = 0
            dumit_portal = "https://www.duemint.com"
            encontrado = False
            
            if results:
                encontrado = True
                
                for row in results:
                    estado = row.get('estado')
                    monto = row.get('monto', 0)
                    
                    if estado == 1:
                        dumit_pagada = monto
                    elif estado == 2:
                        dumit_por_vencer = monto
                    elif estado == 3:
                        dumit_vencida = monto
                    
                    # Obtener URL del portal si existe
                    url_cliente = row.get('url_cliente')
                    if url_cliente:
                        dumit_portal = url_cliente
            
            return {
                'success': True,
                'encontrado': encontrado,
                'rut': rut_limpio,
                'datos': {
                    'pagada': float(dumit_pagada),
                    'por_vencer': float(dumit_por_vencer),
                    'vencida': float(dumit_vencida),
                    'portal_url': dumit_portal
                }
            }
            
    except Exception as e:
        return {
            'success': False,
            'error': f'Error al consultar: {str(e)}'
        }
    
    finally:
        if connection:
            connection.close()
