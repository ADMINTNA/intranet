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

/******************************
 * ORDENAMIENTO (AJAX)
 ******************************/
function sortTable(columnName) {
    let sort = $("#sort").val();
    console.log("Ordenando columna:", columnName, "orden actual:", sort);

    $.ajax({
        url: 'fetch_details.php',
        type: 'post',
        data: { columnName: columnName, sort: sort },
        success: function(response) {
            // Remover filas de datos
            $("#empTable tr:not(:lt(2))").remove(); // mantiene header + filtros
            $("#empTable").append(response);

            // Cambiar valor del orden
            const newSort = (sort === "asc") ? "desc" : "asc";
            $("#sort").val(newSort);

            // 🔽 Actualizar íconos visuales de orden
            updateSortIcons(columnName, newSort);

            console.log("Orden cambiado a:", newSort);
            reapplyClientFilters();
        },
        error: function(xhr) {
            console.log("Error AJAX sortTable:", xhr && xhr.responseText);
        }
    });
}

