// JavaScript Document
// funciones usadas en Intranet
    function mueveReloj(){
        var momentoActual = new Date()
        var hora = momentoActual.getHours()
        var minuto = momentoActual.getMinutes()
        var segundo = momentoActual.getSeconds()
        if(segundo<10) { segundo="0"+segundo; }
        if(minuto<10) { minuto="0"+minuto; }
        if(hora<10) { hora="0"+hora; }
        var horaImprimible = "Hora:"+hora + " : " + minuto + " : " + segundo
        document.getElementById("reloj").innerHTML = horaImprimible
        setTimeout("mueveReloj()",1000)
    }
