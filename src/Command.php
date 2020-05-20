<?php
	namespace equilibrium;

	abstract class Command
	{
	    public function execute(){throw new \Exception('Debe definir el método execute');}
	}
