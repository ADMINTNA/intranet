"""
Aplicación Flask para búsqueda de cuenta corriente Duemint
"""
from flask import Flask, request, jsonify, render_template
from config import buscar_cuenta_duemint

app = Flask(__name__)


@app.route('/')
def index():
    """Página principal con formulario de búsqueda"""
    return render_template('index.html')


@app.route('/api/buscar', methods=['POST', 'GET'])
def buscar():
    """
    API endpoint para buscar cuenta corriente por RUT
    
    Acepta:
        - POST: rut en form data o JSON
        - GET: rut como query parameter
        
    Retorna:
        JSON con información de la cuenta
    """
    # Obtener RUT desde diferentes fuentes
    rut = None
    
    if request.method == 'POST':
        if request.is_json:
            rut = request.json.get('rut')
        else:
            rut = request.form.get('rut')
    else:  # GET
        rut = request.args.get('rut')
    
    if not rut:
        return jsonify({
            'success': False,
            'error': 'RUT no proporcionado'
        }), 400
    
    # Buscar en la base de datos
    resultado = buscar_cuenta_duemint(rut)
    
    if not resultado.get('success'):
        print(f"❌ Error en la búsqueda: {resultado.get('error')}")
        return jsonify(resultado), 500
    
    return jsonify(resultado)


@app.route('/health')
def health():
    """Health check endpoint"""
    return jsonify({'status': 'ok'})


if __name__ == '__main__':
    # Modo desarrollo
    app.run(debug=True, host='0.0.0.0', port=5000)
