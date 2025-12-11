<?php include_once("../session.php");?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
 <html xmlns="http://www.w3.org/1999/xhtml">	 
<head>
<?PHP include_once("../meta_data/meta_data.html"); ?>   
<title><?php //echo $server; ?> Clientes iConTel</title>
<style type="text/css">
	html, body, div, iframe {
		margin:0;
		padding:0;
		height:100%;
		margin-left: 0px;
		margin-top: 0px;
		margin-right: 0px;
		margin-bottom: 0px;
	}
	table { width:100%; border:0px solid lightgrey; }
	iframe { display:block; width:100%;  border:none; }
	footer {
	  background-color: white;
	  position: absolute;
	  bottom: 50;
	  width: 100%;
	  height: 25px;
	  color: gray;
	  font-size: 12px;
	}
	/* unvisited link */
	a:link {
	  color: gray;
	}
	/* visited link */
	a:visited {
	  color: gray;
	}
	/* mouse over link */
	a:hover {
	  color: darkgrey;
	  font-size: 20px;
	  font-weight: bold;
	}
	/* selected link */
	a:active {
	  color: blue;
	}		
</style>
</head>
<body>
<iframe src="cli_prod_new.php" ></iframe>
<?php include_once("../footer/footer.php");?>
</body>
</html>
