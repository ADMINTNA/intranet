function editarPresupuesto(presupuesto) {
    document.getElementById('presupuesto-id').value = presupuesto.id;
    document.getElementById('presupuesto-cliente').value = presupuesto.cliente;
    document.getElementById('presupuesto-direccion').value = presupuesto.direccion;
    document.getElementById('presupuesto-consumoMensual').value = presupuesto.consumoMensual;
    document.getElementById('presupuesto-facturacionMensual').value = presupuesto.facturacionMensual;
    document.getElementById('presupuesto-horasSol').value = presupuesto.horasSol;
    document.getElementById('presupuesto-tamanoPanel').value = presupuesto.tamanoPanel;
    document.getElementById('presupuesto-cantidadPaneles').value = presupuesto.cantidadPaneles;
    document.getElementById('presupuesto-m2Paneles').value = presupuesto.m2Paneles;
    document.getElementById('presupuesto-potenciaPanel').value = presupuesto.potenciaPanel;
    document.getElementById('presupuesto-rendimientoPanel').value = presupuesto.rendimientoPanel;
    document.getElementById('presupuesto-cantidadPlantas').value = presupuesto.cantidadPlantas;
    document.getElementById('presupuesto-kWhPlanta').value = presupuesto.kWhPlanta;
    document.getElementById('presupuesto-kWMes').value = presupuesto.kWMes;
    document.getElementById('presupuesto-cumplimiento').value = presupuesto.cumplimiento;
    document.getElementById('presupuesto-margenComercial').value = presupuesto.margenComercial;

    document.querySelector('input[name="accion"]').value = 'editar';
}
