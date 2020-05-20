<?php
	namespace equilibrium\model_generator;
	
	class MssqlGenerator extends AbstractGenerator
	{
		protected $_adapterName = 'mssql';
		
		public function getDsn()
		{
			if (empty ($this->_dsn))
			{
				$this->_dsn = 'sqlsrv:server=' . $this->_server;
				if (!empty($this->_port)) $this->_dsn .= ',' . $this->_port;
				$this->_dsn .= ';Database=' . $this->_dbname;
			}
				
			return $this->_dsn;
		}
		
	}