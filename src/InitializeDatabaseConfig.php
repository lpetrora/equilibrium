<?php
	namespace equilibrium;
	use Equilibrium;
	if (Equilibrium::config()->database !== null)
	{
		$conf = Equilibrium::config()->database;
		$defaultDatasource = $conf->defaultDataSource;
		foreach ($conf->dataSources as $name => $ds)
		{
			$serviceContainer = \Propel\Runtime\Propel::getServiceContainer();
			//$serviceContainer->checkVersion('2.0.0-dev');
			$serviceContainer->setAdapterClass($name, $ds->adapter);
			$manager = new \Propel\Runtime\Connection\ConnectionManagerSingle();
			$config = [
					'classname' => 'Propel\\Runtime\\Connection\\ConnectionWrapper',
					'dsn' => $ds->adapter.':host='.$ds->server.';port='.$ds->port.';dbname='.$ds->database,
					'user' => $ds->user,
					'password' => $ds->password,
					'attributes' => [],
					//array (
					//	'ATTR_EMULATE_PREPARES' => false,
					//),
					'settings' => [],
					//			'model_paths' => [
							//					0 => 'src',
							//					1 => 'vendor'
							//				]
			];
			if (strtolower($ds->charset) == 'utf-8' || strtolower($ds->charset) == 'utf8')
			{
				$config['settings']['charset'] = 'utf8';
				$config['settings']['queries'][] = 'SET NAMES utf8 COLLATE utf8_unicode_ci, COLLATION_CONNECTION = utf8_unicode_ci, COLLATION_DATABASE = utf8_unicode_ci, COLLATION_SERVER = utf8_unicode_ci';
			}
		
			$manager->setConfiguration($config);
			$manager->setName($name);
			$serviceContainer->setConnectionManager($name, $manager);
		}
		$serviceContainer->setDefaultDatasource($defaultDatasource);
	}