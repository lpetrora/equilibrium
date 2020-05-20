<?php
$log = [
	'name' => 'equilibrium',
	'handlers' => [
		['StreamHandler', [Equilibrium::$appPath . DIRECTORY_SEPARATOR . 'log' . DIRECTORY_SEPARATOR . 'log.txt']]
	]
];

return $log;