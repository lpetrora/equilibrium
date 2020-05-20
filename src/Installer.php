<?php
	namespace equilibrium;
		
	use equilibrium\model_generator\AbstractGenerator;
	
	class Installer
	{
	    static protected $directories = [
	        'command' => 0755,
	        'controllers' => 0755,
	        'config' => 0755,
	        'helpers' => 0755,
	        'models' => 0755,
	        'log' => 0777,
	        'views' => 0755,
	        'views/_cache' => 0777,
	        'views/_plugins' => 0755,
	        'web' => 0755	        
	    ];
	    
		static protected $appPath = null;
		static protected $vendorPath = null;
		static protected $vendorDir = null;
		static protected $equilibriumPath = null;
		
		static public function setAppPath($v) {
		    static::$appPath = $v;
		}
		
		static public function setVendorPath($v) {
		    static::$vendorPath = $v;
		}
		
		static public function setEquilibriumPath ($v) {
		    static::$equilibriumPath = $v;
		}
		
		static public function getAppPath () {
		    if (empty(static::$appPath)) {
			static::$appPath = dirname(static::$vendorPath);
		    }
		    
		    return static::$appPath;
		}
		
		static public function getVendorPath () {
		    if (empty(static::$vendorPath)) {
			static::$vendorPath = realpath(dirname(dirname(dirname(static::$equilibriumPath))));
		    }
		    
		    return static::$vendorPath;
		}
		
		static public function getEquilibriumPath () {
		    if (empty(static::$equilibriumPath)) {
			static::$equilibriumPath = realpath(dirname(__FILE__));
		    }
		    
		    return static::$equilibriumPath;
		}
		
		static public function getVendorDir () {
		    if (empty(static::$vendorDir)) {
    		    $path = static::getVendorPath();
    		    if (substr($path, -1, 1) != DIRECTORY_SEPARATOR) $path .= DIRECTORY_SEPARATOR;
    		    $path = explode (DIRECTORY_SEPARATOR, $path);
    		    array_pop($path);
    		    static::$vendorDir = array_pop ($path);		        
		    }
		    return static::$vendorDir;
		}
		
		static protected function copyDirectory ($from, $to)
		{
		    $dir = opendir($from);
		    @mkdir($to);
		    
		    while ($file = readdir($dir)) {
		        if ( $file == '.' ) continue;
		        if ( $file == '..' ) continue;
		        if ( is_dir(file) ) {
		            static::copyDirectory($from . DIRECTORY_SEPARATOR . $file, $to . DIRECTORY_SEPARATOR . $file);
                    continue;          
		        }
		        
		        copy ($from . DIRECTORY_SEPARATOR . $file, $to . DIRECTORY_SEPARATOR . $file);
		    }
		}
		
		static protected function createHtaccess()
		{
			$content = '';
			$content .= '<FilesMatch "\.(htaccess|htpasswd|ini|log|sh)$">'.PHP_EOL;
			$content .= '  Require all denied'.PHP_EOL;
			$content .= '</FilesMatch>'.PHP_EOL;
			$content .= '<IfModule mod_rewrite.c>'.PHP_EOL;
			$content .= '  RewriteEngine On'.PHP_EOL;
			$content .= '  RewriteCond %{REQUEST_FILENAME} !-f'.PHP_EOL;
			$content .= '  RewriteRule ^.*$ index.php [NC,L]'.PHP_EOL;
			$content .= '</IfModule>'.PHP_EOL;
			$content .= '<IfModule !mod_rewrite.c>'.PHP_EOL;
			$content .= '  ErrorDocument 404 index.php'.PHP_EOL;
			$content .= '</IfModule>'.PHP_EOL;
			@file_put_contents(static::getAppPath() . DIRECTORY_SEPARATOR . 'web' . DIRECTORY_SEPARATOR . '.htaccess', $content);
		}
		
		static protected function createIndex()
		{
			$vendorDir = static::getVendorDir();
			$content = '';
			$content .= '<?php'.PHP_EOL;
			$content .= "  require_once dirname(__DIR__).'/$vendorDir/autoload.php';".PHP_EOL;
			$content .= '  use equilibrium\Bootstrap;'.PHP_EOL;
			$content .= '  $httpDir = getenv(\'APP_HTTP_DIRECTORY\');'.PHP_EOL;
			$content .= '  $httpDir = !empty($_ENV[\'REDIRECT_APP_HTTP_DIRECTORY\'])?$_ENV[\'REDIRECT_APP_HTTP_DIRECTORY\']:!empty($httpDir)?$httpDir:\'/\';'.PHP_EOL;
			$content .= '  $b = new Bootstrap($httpDir);'.PHP_EOL;
			$content .= '  $b->run();'.PHP_EOL;
			@file_put_contents(static::getAppPath() . DIRECTORY_SEPARATOR . 'web' . DIRECTORY_SEPARATOR . 'index.php', $content);
		}
		
		static public function mkdirs($event)
		{
		    $base = static::getAppPath();
		    foreach (static::$directories as $dir => $perm)
		        @mkdir($base . DIRECTORY_SEPARATOR . $dir, $perm);
		        			
			static::chmods($event);
			static::createHtaccess();
			static::createIndex();
			
			$from = static::getEquilibriumPath() . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'doc' . DIRECTORY_SEPARATOR . 'config'; 
			$to = static::getAppPath() . DIRECTORY_SEPARATOR . 'config';
			static::copyDirectory($from, $to);
		}
		
		static public function chmods($event)
		{
			$base = static::getAppPath();
			foreach (static::$directories as $dir => $perm)
			    @chmod($base . DIRECTORY_SEPARATOR . $dir, $perm);
			
			@unlink ($base . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . '_cache' . DIRECTORY_SEPARATOR . '*.php'); 
		}
		
		static public function launch($command, $params = [])
		{
			require_once static::getVendorPath() . DIRECTORY_SEPARATOR . 'autoload.php';
			
			if (empty($command)) static::actionHelp();
			
			$com = $command;
			$command = 'action' . ucfirst($command);
			
			if (method_exists('equilibrium\Installer', $command))
			{
				static::$command($params);
			} else {
				echo "Error, el comando '$com' no existe". PHP_EOL;
				static::actionHelp();
			}
		}
		
		static protected function actionChmods($args)
		{
			static::chmods(null);
		}
		
		static protected function actionMkdirs($args)
		{
			static::mkdirs(null);
		}
		
		static protected function actionHelp()
		{
			echo "Comandos soportados: ".PHP_EOL;
			echo "  mkdirs                              Crea los directorios para el proyecto".PHP_EOL;
			echo "  chmods                              Establece los permisos para los directorios del proyecto".PHP_EOL;
			echo "  reverse_model  [datasource]         Genera las clases de modelo de una base de datos definida en la configuracion".PHP_EOL;
			echo "  run <command> [p1..pn]              Ejecuta el comando <command>".PHP_EOL;
			echo "  help                                Esta pantalla".PHP_EOL;
			exit(0);
		}
		
		static protected function composer()
		{	$filename = static::getAppPath() . DIRECTORY_SEPARATOR . 'composer.json';
			$f = json_decode(file_get_contents($filename),true);
			//Modificar cosas aca.
			$f = json_encode($f,JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
			file_put_contents($filename, $f);
		}
		
		static protected function actionReverse_model ($params)
		{
			$propelConfig = [];
			$configDir = static::getAppPath() . DIRECTORY_SEPARATOR . 'config';
			$sqlDir = static::getAppPath() . DIRECTORY_SEPARATOR . 'sql';
			@mkdir ($sqlDir);
			@unlink ($sqlDir.'/*');
			$currDir = getcwd();
			chdir($sqlDir);
			
			//Leer la configuración
			$cfgFile = $configDir. DIRECTORY_SEPARATOR .'config.php';
			$privCfgFile = $configDir. DIRECTORY_SEPARATOR . 'private-config.php';
			$cfg = include $cfgFile;
			
			if (is_readable($privCfgFile)){
			    $privCfg = include $privCfgFile;
			    $cfg = array_merge($cfg, $privCfg);
			}
						
			$cfg = $cfg['database']??null;
			
			if (empty ($cfg)) 
			    die ('No hay configuración de base de datos');
			
			$defaultDatasource = $cfg['defaultDataSource'];
			$datasource = empty($params)?$defaultDatasource:$params[0];
			$dsnWithCredentials = '';
			$adapter = '';
			
			if (! isset($cfg['dataSources'][$datasource]))
			{
				echo "No existe el datasource '$datasource' en la configuracion".PHP_EOL;
				die();
			}
			
			foreach ($cfg['dataSources'] as $dataSourceName => $cfgDatasource)
			{
				$gen = AbstractGenerator::build($cfgDatasource['adapter']);
				$gen->addConfig($datasource, $cfgDatasource, $propelConfig);
				if ($dataSourceName == $datasource) 
				{
					$dsnWithCredentials = $gen->getDsnWithCredentials();
					$adapter = $gen->getAdapterName();
				}
			}

			//Configuración adicional para el runtime
			$propelConfig['propel']['runtime']['defaultConnection'] = $defaultDatasource;

			//Configuración para el generador. Vamos a generar 1 solo datasource.
			$propelConfig['propel']['generator']['defaultConnection'] = $defaultDatasource;
			$propelConfig['propel']['generator']['connections'] = [$datasource];
			
			//Solo si estoy haciendo un reverse de MySQL
			if ($adapter == 'mysql')
				$propelConfig['propel']['migrations']['parserClass'] = 'equilibrium\data\CustomMysqlSchemaParser';
			
			$f = json_encode($propelConfig,JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
			file_put_contents($sqlDir . '/propel.json', $f);
	
			//Genero el archivo schema.xml
			$propelbin = static::getVendorPath() . DIRECTORY_SEPARATOR . 'bin' . DIRECTORY_SEPARATOR . 'propel';

			//linea de comandos para hacer reverse
			$cmd = "$propelbin reverse \"$dsnWithCredentials\" --output-dir $sqlDir --database-name $datasource -n -vv";
			$return = 0;
			system ($cmd, $return);
			
			if ($return != 0)
			{
				'Ocurrio un error al intentar hacer reverse de la base de datos'.PHP_EOL;
				chdir ($currDir);
				die();
			}

			//Agregar el namespace
			//$namespace = 'app\\models\\'.$datasource;
			$namespace = 'app\\models';
			$xml = simplexml_load_file($sqlDir . '/schema.xml');
			$xml->addAttribute('namespace', $namespace);
			$xml->saveXML($sqlDir . '/schema.xml');
			
			//Generar las clases de modelo
			//$cmd = "$propelbin model:build --output-dir=" . static::$appDir .'/models/' . $datasource . ' --disable-namespace-auto-package -n';
			$cmd = "$propelbin model:build --output-dir=" . static::getAppPath() .'/models --disable-namespace-auto-package -n';

			$return = 0;
			system ($cmd, $return);
			if ($return != 0)
			{
				'Ocurrio un error al intentar hacer reverse de la base de datos'.PHP_EOL;
				chdir ($currDir);
			}
			//system('/bin/rm -R ' . $sqlDir);
			
			chdir ($currDir);
		}
		
		static protected function actionRun ($args)
		{
			$command = array_shift($args);
			
			if (empty($command))
			{
				echo "Debe especificar el nombre del comando".PHP_EOL;
				static::actionHelp();
			}
			
			//var_dump ($controller, $method, $args);
			include __DIR__ . DIRECTORY_SEPARATOR . 'BootstrapCli.php';
			$launcher = new BootstrapCli();
			$launcher->run($command,$args);
		}
	}