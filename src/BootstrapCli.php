<?php
	namespace equilibrium;
	require_once dirname(__DIR__).'/autoload.php';
	require_once __DIR__.'/exceptions/ErrorException.php';

	use Equilibrium;
	
	class BootstrapCli {
		
		protected $_http = '/';
		protected $_basePath = '';
		
		public function __construct()
		{
			//$this->_http = $httpDir;
			$this->_basePath = realpath(__DIR__.'/../../');
		}
		
		protected function setup()
		{
		    session_save_path(sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'equilibrium' . DIRECTORY_SEPARATOR. 'session');
		    
			Equilibrium::$appPath = $this->_basePath;
			Equilibrium::$equilibriumPath = __DIR__;
			
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
		
		public function run ($command, $args = [])
		{
		    $this->setup();
		    
		    try {
		        $path = $this->_basePath . '/commands/'.$command.'.php';
		        if (! is_readable($path)) 
		            throw new \Exception("No se encuentra la clase $command en '$path'");
		        
		        require_once $this->_basePath . '/commands/'.$command.'.php';
		        $tmp = "app\commands\\$command";
		        $object = new $tmp();
		        
                call_user_func_array([$object, 'execute'], $args);
		        
		    } catch (\Exception $e) {
		        Equilibrium::log()->critical($e);
		        echo 'ERROR: ' . $e->getMessage() . PHP_EOL;
		    }
		}
		
		/*
		protected function fileControllerExists($class)
		{
			$path = $this->_basePath ."/controllers/$class.php";
			return $this->fileExists($path);
			
		}
		
		protected function fileExists($filename)
		{
			return is_file($filename) && is_readable($filename);
		}

		public function run($controller, $method, $args = [])
		{
			$this->setup();
			
			try {
				//if (! $this->fileControllerExists($parsed['class'])) $this->returnNotFoundPage();
				require_once $this->_basePath . '/controllers/'.$controller.'.php';
				$tmp = "app\controllers\\$controller";
				$object = new $tmp();
				if (is_callable([$object, $method]))
				{
					$object->processPageParameters();
					call_user_func_array([$object, $method], $args);
				}
				
			} catch (Exception $e) {
				var_dump ($e);
			}
		}
		*/
	}