/******************************
 * EXPORTAR A EXCEL
 ******************************/
function exportToExcel(tableId){
    var tableData = document.getElementById(tableId).outerHTML;
    tableData = tableData.replace(/<A[^>]*>|<\/A>/gi, "");
    tableData = tableData.replace(/<input[^>]*>|<\/input>/gi, "");

    var a = document.createElement('a');
    a.href = 'data:application/vnd.ms-excel,' + encodeURIComponent(tableData);
    a.download = 'ServicioVigentes_' + getRandomNumbers() + '.xls';
    a.click();
}

function getRandomNumbers(){
    var d = new Date();
    var dateTime = '' + d.getHours() + d.getMinutes() + d.getSeconds();
    return dateTime + Math.floor((Math.random().toFixed(2) * 100));
}

/******************************
 * HELPERS
 ******************************/
function inArray(arr, val){
    for (var i=0; i<arr.length; i++) if (arr[i] === val) return true;
    return false;
}
function strStartsWith(str, prefix){
    return str && prefix && str.lastIndexOf(prefix, 0) === 0;
}

