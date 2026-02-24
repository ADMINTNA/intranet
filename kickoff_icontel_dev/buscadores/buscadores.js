// JavaScript Document
		$(function () {
	/*		$('select[multiple].active.3col').multiselect({ // 3 columnas
				columns: 3,
				placeholder: 'Selecionar de la Lista',
				search: true,
				searchOptions: {
					'default': 'Buscar'
				},
				selectAll: false 
			});
*/
			$('select[multiple]').multiselect(); // básico
		});
		
        function resetForm(form) {
			document.getElementById(form).reset();
			$('select[multiple]').multiselect( 'reset' );
		}