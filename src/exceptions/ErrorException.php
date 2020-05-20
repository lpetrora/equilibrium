<?php
	function exception_error_handler($severidad, $mensaje, $fichero, $linea) 
	{
		//Error not included in error_reporting
		if (!(error_reporting() & $severidad)) return;
		Equilibrium::log()->critical("$fichero ($linea): $mensaje");
		throw new ErrorException($mensaje, 0, $severidad, $fichero, $linea);
	}
	set_error_handler("exception_error_handler");
