<?php 
$config = [
	'application' => [
		'debug' => true,
		'charset' => 'UTF-8',
		'timezone' => 'America/Argentina/Buenos_Aires',
		//'userHandler' => 'app\helpers\user\User',
		'prefix' => '/',
	],
	
	'database' => [
		'defaultDataSource' => 'myDatabaseSource',
		'dataSources' => [
			'myDatabaseSource' => [
				'adapter' => 'mysql',
				'server' => 'host',
				'port' => port,
				'user' => 'user',
				'password' => 'secret' ,
				'database' => 'myDatabase',
				'charset' => 'utf8'
			],
		],
	],
];

return $config;