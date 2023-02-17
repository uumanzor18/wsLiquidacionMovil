<?php 

	

	function writeLog($mensaje, $linea) {  

		$handle = fopen("/opt/cepheuslog/wsLiquidacionMovil.log","a+");

		fwrite($handle, date("[j-M-Y-H:i:s]") . ": " . $linea . " => " . $mensaje . ";\n");

		fclose($handle);

	}



	function writeActionLog($accion, $contexto) {  

		$handle = fopen("/opt/cepheuslog/wsLiquidacionMovil.log","a+");

		fwrite($handle,date("[j-M-Y-H:i:s]") .": " . $accion . " => " . $contexto. ";\n");

		fclose($handle);

	}



	function cleanedString($string){

		$cleanString = array("@","\$","^","´","&","%","*","+","'","\\","[","]","/","{","}","|",":","<",">","?");

	    $response = null;

	    $response = str_replace($cleanString, "", trim($string));



	    return preg_replace('!\s+!', ' ', strtolower($response));

	 }



 ?>

