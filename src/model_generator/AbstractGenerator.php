<?php
	namespace equilibrium\model_generator;
	
	abstract class AbstractGenerator
	{
		
		protected $_adapterName = '';
		protected $_username = '';
		protected $_password = '';
		protected $_server = '';
		protected $_port = 0;
		protected $_dbname = '';
		protected $_dsn = '';

		/**
		 * Returns proper class to handle adapter
		 * @param string $adapter
		 * @return AbstractGenerator
		 */
		static public function build($adapter)
		{
			$adapter = \Equilibrium::toUpperCamelcase($adapter);
			try {
				$class = 'equilibrium\model_generator\\'.$adapter.'Generator';
				$result = new $class();
				
			} catch (\Exception $e) {
				die('Adaptador desconocido: $adapter');
			}
			return $result;
		}
		
		public function getAdapterName()
		{
			return $this->_adapterName;
		}
		
		public function getDsn()
		{
			if (empty ($this->_dsn))
			{
				$this->_dsn = $this->_adapterName . ':host=' . $this->_server;
				if (!empty($this->_port)) $this->_dsn .= ';port=' . $this->_port; 
				$this->_dsn .= ';dbname=' . $this->_dbname;
			}
			
			return $this->_dsn;
		}
		
		public function getDsnWithCredentials()
		{
			$dsn = $this->getDsn();
			$dsn .= ';user=' . $this->_username . ';password='.$this->_password;
			return $dsn;
		}
		
		public function addConfig($datasourceName, $datasourceConfig, & $config)
		{
			$this->_dsn = isset($datasourceConfig['dsn'])?$datasourceConfig['dsn']:'';
			$this->_server = isset($datasourceConfig['server'])?$datasourceConfig['server']:'';
			$this->_port = isset($datasourceConfig['port'])?$datasourceConfig['port']:'';
			$this->_dbname = $datasourceConfig['database'];
			$this->_username = $datasourceConfig['user'];
			$this->_password = $datasourceConfig['password'];
			
			if (empty($this->_dsn)) $this->getDsn();
			
			$config ['propel']['database']['connections'][$datasourceName]['adapter'] = $this->_adapterName;
			$config ['propel']['database']['connections'][$datasourceName]['classname'] = 'Propel\Runtime\Connection\ConnectionWrapper';
			$config ['propel']['database']['connections'][$datasourceName]['dsn'] = $this->_dsn;
			$config ['propel']['database']['connections'][$datasourceName]['user'] = $this->_username;
			$config ['propel']['database']['connections'][$datasourceName]['password'] = $this->_password;
			$config ['propel']['database']['connections'][$datasourceName]['attributes'] = [];
				
			if (isset($datasourceConfig['charset']) && ((strtolower($datasourceConfig['charset']) == 'utf-8') || (strtolower($datasourceConfig['charset']) == 'utf8')))
			{
				$config ['propel']['database']['connections'][$datasourceName]['settings']['charset'] = 'utf8';
				$config ['propel']['database']['connections'][$datasourceName]['settings']['queries'] = ['SET NAMES utf8 COLLATE utf8_unicode_ci, COLLATION_CONNECTION = utf8_unicode_ci, COLLATION_DATABASE = utf8_unicode_ci, COLLATION_SERVER = utf8_unicode_ci'];
			}
				
			$config ['propel']['runtime']['connections'][] = $datasourceName;
		
			return $config;
		}
	}