<?php
	namespace equilibrium;
	require_once __DIR__.'/exceptions/ErrorException.php';

	use Equilibrium;
	use equilibrium\AuthManager;
	use equilibrium\RouteManager;
	
	class Bootstrap {
		
		static public $appPath = null;
		static protected $vendorPath = null;
		static public $equilibriumPath = null;
		
		protected $_http = '/';
		protected $_basePath = '';
		
		public function __construct()
		{
		    $equilibrium_path = realpath(__DIR__);
		    $vendor_path = realpath(dirname(dirname(dirname($equilibrium_path))));
		    $app_path = realpath(dirname($vendor_path));
		    		
		    static::$appPath = $app_path;
		    static::$vendorPath = $vendor_path;
		    static::$equilibriumPath = $equilibrium_path;
		    
			$this->_basePath = $app_path;
		}
		
		protected function setup()
		{
		    \Equilibrium::$appPath = static::$appPath;
		    \Equilibrium::$equilibriumPath = static::$equilibriumPath;
		    
			$httpDir = \Equilibrium::config()->application->prefix??'/';
			$this->_http = $httpDir; //TODO:Empieza y termina con /
			\Equilibrium::$prefix = $httpDir;
			
			$charset = Equilibrium::config()->application->charset;
			header('Content-Type:text/html; charset='.$charset);
			ini_set('default_charset',$charset);
			date_default_timezone_set(Equilibrium::config()->application->timezone);
			
			if (Equilibrium::config()->application->debug)
			{
				ini_set('display_errors','1');
				error_reporting(E_ALL);
			} else {
				ini_set('display_errors','0');
			}
			
			require_once 'InitializeDatabaseConfig.php';
		}
		
		protected function fileControllerExists($class)
		{
			$path = $this->_basePath ."/controllers/$class.php";
			return $this->fileExists($path);
		}
		
		protected function fileExists($filename)
		{
			return is_file($filename) && is_readable($filename);
		}

		protected function returnNotFoundPage()
		{
			header("HTTP/1.0 404 Not Found");
			die('Not found');
		}
		
		public function run()
		{
			$this->setup();
			$parsed = RouteManager::makeRoute($this->_http);
			
			if (! $this->fileControllerExists($parsed['class'])) $this->returnNotFoundPage();
			require_once $this->_basePath . '/controllers/'.$parsed['class'].'.php';
			
			$className = $parsed['namespace'].$parsed['class'];
			$object = new $className();
			Equilibrium::$currentController = $object;
			
			if (! is_callable([$object, $parsed['action']]) && $parsed['canFallBack'])
			{
				$parsed['action'] = 'actionIndex';
				$parsed['params'] = $parsed['fallbackParams'];
			}

			//Check authorization
			$user = Equilibrium::user();
			$isAuth = AuthManager::isAllowed($parsed['class'], $parsed['action'], $user);
			if (! $isAuth)
			{
				if (\Equilibrium::config()->application->debug) \Equilibrium::log()->debug('Autorización denegada: ', $parsed );
				
				$redirector = explode('::',AuthManager::$lastCallback);
				$className = $redirector[0];
				$parsed['class'] = explode('\\',$redirector[0]);
				$parsed['class'] = end($parsed['class']);
				$parsed['action'] = $redirector[1];
				try {
					$action = lcfirst(substr($parsed['action'],6));
					$reflectionClass = new \ReflectionClass($className);
					$method = $reflectionClass->getMethod('redirectAndCall');
					$method->invoke(null, $action);
					
				} catch (\Exception $e) {
					if (! $this->fileControllerExists($parsed['class'])) $this->returnNotFoundPage();
				}
			}
			
			//Acá hay que cambiar un poco las cosas, las acciones tienen que devolver un objeto response
			
			try {
				//Llamado a la clase
				$object->processPageParameters();
				call_user_func_array([$object, $parsed['action']], $parsed['params']);
				die();
				
			} catch (\Exception $e) {
				//TODO: Si estoy en modo debug, mostrar el error, sino tirar la pantalla azul de la muerte.
				\Equilibrium::log()->critical($e);
				if (\Equilibrium::config()->application->debug) 
				{
					var_dump ($e);
				} else {
					http_response_code(500);
					die ('Unhandled but logged error');
				}
			}
		}
	}