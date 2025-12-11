	function exportToExcel(tableId){
		let tableData = document.getElementById(tableId).outerHTML;
		tableData = tableData.replace(/<A[^>]*>|<\/A>/g, ""); //remove if u want links in your table
		tableData = tableData.replace(/<input[^>]*>|<\/input>/gi, ""); //remove input params

		let a = document.createElement('a');
		a.href = `data:application/vnd.ms-excel, ${encodeURIComponent(tableData)}`
		a.download = 'ServicioVigentes_' + getRandomNumbers() + '.xls'
		a.click()
	}
	function getRandomNumbers() {
		let dateObj = new Date()
		let dateTime = `${dateObj.getHours()}${dateObj.getMinutes()}${dateObj.getSeconds()}`

		return `${dateTime}${Math.floor((Math.random().toFixed(2)*100))}`
	}        
	function sortTable(columnName){

		var sort = $("#sort").val();
		$.ajax({
			url:'fetch_details.php',
			type:'post',
			data:{columnName:columnName,sort:sort},
			success: function(response){

				$("#empTable tr:not(:first)").remove();

				$("#empTable").append(response);
				if(sort == "asc"){
					$("#sort").val("desc");
				}else{
					$("#sort").val("asc");
				}

			}
		});
	}
