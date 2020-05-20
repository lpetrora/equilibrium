<?php
	namespace equilibrium;
	use Equilibrium;
	
	class Config
	{
		protected $config = [];
		protected $aConfig = [];
		
		protected function loadConfiguration($cfg)
		{
			foreach ($cfg as $key=>$config)
			{
				//Convierto la configuración a objeto anonimo.
				$this->config[$key] = $this->arrayToObject($config);
			}
		}
		
		public function __construct($path = null)		
		{
			if ($path == null) $path = Equilibrium::$appPath. DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php';
			$file = basename($path);
			$dir = substr($path,0 ,-1 * strlen($file));
		
			$cfg = include($dir. $file);
			$this->aConfig = $cfg;
			$this->loadConfiguration($cfg);
			if (is_readable($dir.'private-'.$file))
			{
				$cfg = include($dir.'private-'.$file);
				$this->aConfig = array_merge($this->aConfig, $cfg);
				$this->loadConfiguration($cfg);
			}
		}	
		
		/**
		 * @desc convertir un array en una instancia de StdClass
		 * @param array $d
		 * @return StdClass
		 */
		protected function arrayToObject($d)
		{
			if (is_array($d)) {
				return (object) array_map(array($this, 'arrayToObject'), $d);
			}
			else {
				return $d;
			}
		}
		
		public function __get($property)
		{
			if (isset($this->config[$property])) return $this->config[$property];
			return null;
		}
		
		public function toArray()
		{
			return $this->aConfig;
		}
	}