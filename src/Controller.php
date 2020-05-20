<?php
	namespace equilibrium;
	use Equilibrium;
	use equilibrium\PageParameters;
	
	abstract class Controller
	{
		public $checkCSRF = true;
				
		/**
		 * Devuelve el objeto en formato json
		 * @param array $args
		 */
		protected function returnJSON($args, $code = 200)
		{
		    http_response_code($code);
			header('Content-Type: application/json');
			echo json_encode($args);
		}
		
		/**
		 * Muestra un template renderizado
		 * @param string $templateName
		 * @param array $args
		 */
		protected function render ($templateName, $args = array())
		{
			echo $this->fetch($templateName, $args);
		}
		
		/**
		 * Devuelve un template renderizado
		 * @param string $templateName
		 * @param array $args
		 * @return string
		 */
		protected function fetch ($templateName, $args = array())
		{
			$directory = explode('\\',get_class($this));
			$directory = end($directory);
			$directory = preg_replace('/Controller$/','',$directory);
			$viewRootDirectory = Equilibrium::$appPath . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR;
			$cacheDirectory = $viewRootDirectory . '_cache';
			$viewDirectory =  $viewRootDirectory . $directory;

			$s = new \Smarty();
			$s->setTemplateDir($viewDirectory);
			$s->setCompileDir($cacheDirectory);
			$s->debugging = Equilibrium::config()->application->debugView;
			$s->addPluginsDir(Equilibrium::$equilibriumPath.'/smarty_plugins');
			$s->addPluginsDir($viewRootDirectory .'_plugins');
			
			//Token CSRF
			$args['__CSRF__'] = Equilibrium::getCSRFToken(); 

			foreach ($args as $var=>$val)
			{
				$s->assign($var, $val);
			}
			return $s->fetch($templateName .'.tpl');
		}
		
		/**
		 * Redirige a una URL
		 * @param string $url
		 */
		protected function redirectToUrl($url)
		{
			header ("location: $url");
			die();
		}
		
		/**
		 * Redirige al controlador y llama al método index
		 * @param PageParameters $pp
		 * @param array $query
		 */
		static function redirect(PageParameters $pp = null, $query = [])
		{
			static::redirectAndCall(null, $pp, $query);
			die();
		}
		
		/**
		 * Redirige al controlador y llama al método $method
		 * @param string $method
		 * @param PageParameters $pp
		 * @param array $query
		 */
		static function redirectAndCall($method, PageParameters $pp = null, $query = [])
		{
			$class = get_called_class()	;
			if ($pp != null)
			{
				PageParameters::registerPageParemeters($class, $pp);			
			}
			$class = explode('\\', $class);
			$class = end($class);
			$class = lcfirst(preg_replace('/Controller$/','',$class));
			$location = \Equilibrium::$prefix;
			$location .= $class;
			$location .= ($method === null)?'':'/'.$method;
			if (! empty($query))
			{
				$location .= '?'.http_build_query($query);
			}
				
			header('location: ' . $location);
			die();
		}
		
		public function processPageParameters()
		{
			$pp = PageParameters::retrievePageParameters($this);
			if ($pp != null)
			{
				$pp = $pp->getParameters();	
				foreach ($pp as $field => $value)
				{
					$this->{$field} = $value;
				}
			}
		}
	}