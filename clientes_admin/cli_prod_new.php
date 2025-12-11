<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>Enalces de TNA SOLUTIONS</title>
<link type='text/css' rel='stylesheet' href='style.css' />
<?php
	#es: esta es la forma RECOMENDADA de incluir los archivos auxiliares (CSS,JS)
	#en: this is the recommended way to include the auxiliary files (CSS,JS)
	include_once('class/phpmydatagrid.class.php');
	echo set_DG_Header("js/","css/", " /", "lightgray");
?>
<style type="text/css">
.activedata{background:#D3FFC9;}
.inactivedata{background:#FCE5E8;}
.miss_date{font-weight:bold;color:#F00;}
.fondoverde{background: lightgreen; border:none;}
.fondorojo{background: #FAB4B5; border:none;}
.fondoamarillo{background: lightyellow; border:none;}
.fondoamarillo{background: lightblue; border:none;}
.fondoazul{background: #F8F38D; border:none;}
.fondogris{background: lightgrey; border:none;}
.colorverde{color: green; border:none; font-weight: bold;}
.colorrojo{color: red; border:none; font-weight: bold;}
.colornaranjo{color: orange; border:none; font-weight: bold;}
.coloramarillo{color: yellow; border:none;}
.colorazul{color: blue; border:none;}
.colorgris{color: grey; border:none;}
.bold{font-weight: bold; font-size: 100%;	}
.normal{font-weight: normal;}
.activedata{background:#D3FFC9;}
.sinfactibilidad{background:lightgrey; color: grey; border:none;}
.inactivo{font-weight:bold;color:grey;}

    #formFiltro { padding:0px; }
    #formFiltro label{ margin-left:10px; display:block; width:80px; float:left; }
    #formFiltro input{ margin-right:10px; float: left; width:130px; }

</style>
<script type="text/javascript" language="javascript">
  	function productos() { window.location = "prod_cli_new.php";	}  	
	function meses_contrato() { window.open('query_update.php','_blank'); }

    function activateSearchBox(){
        curDisplay = document.getElementById('formFiltro').style.display;
        DG_Slide("formFiltro",{duration:.2}).swap()
        if (curDisplay=='none') document.getElementById('fname').focus();
    }

	var lastID = ""; /* Used to store the last selected Row ID */
	var currentGrid= ""; /* Used to store the Grid ID */

	function viewDetails(id){
		/* Restore last selected row */
        oldClass = DG_gvv('dg' + currentGrid+'Choc'+lastID);
		if (lastID!="" && typeof(oldClass) != 'undefined') DG_goo('dg' + currentGrid +'TR'+lastID).className = oldClass;

		/* Store Last selected row info */
		currentGrid = ac(); lastID = id;

		/* Set the new class (background) for the selected row */
		DG_goo('dg' + currentGrid + 'TR' + id).className='dgSelRowDetails';

		/* Select details Grid */
		DG_set_working_grid("2");  /* This must be the code of the grid to be updated */

		/* Execute Grid call */
		DG_Do("", "&e_id="+id);
	}
</script>

</head>

<body>
	<table border="0" id="bg">
	  <tr>
			<td id="content2" style="vertical-align:top; text-align:left">
				<div id='dg'>
					<?php include_once("clientes_new.php"); ?>
				</div>
			</td>
    </tr>
	</table>
</body>
</html>
