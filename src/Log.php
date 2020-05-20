<?php
namespace equilibrium;
use Equilibrium;
use Monolog\Logger;

class Log 
{
	protected $_logger = null;

	public function __construct()
	{
		if (is_readable(Equilibrium::$appPath. DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'log.php'))
		{
			$cfg = include (Equilibrium::$appPath.'/config/log.php');
			//Procesar la configuración
			if (isset($cfg['handlers']) && !empty($cfg['handlers']))
			{
				$this->_logger = new Logger($cfg['name']);

				foreach ($cfg['handlers'] as $handler)
				{
					$class = $handler[0];
					$args = isset($handler[1])?$handler[1]:[];
					$reflect  = new \ReflectionClass("Monolog\Handler\\$class");
					$instance = $reflect->newInstanceArgs($args);
					$this->_logger->pushHandler($instance);
				}
			}
			if (isset($cfg['customHandlers']) && !empty($cfg['customHandlers']))
			{
				foreach ($cfg['customHandlers'] as $handler)
				{
					$this->_logger->pushHandler($handler);
				}
			}
			
		}
	}
	
	public function debug ($string, $context = [])
	{
		if ($this->_logger !== null) $this->_logger->addRecord(Logger::DEBUG, $string, $context);
	}
	
	public function info ($string, $context = [])
	{
		if ($this->_logger !== null) $this->_logger->addRecord(Logger::INFO, $string, $context);
	}
	
	public function notice ($string, $context = [])
	{
		if ($this->_logger !== null) $this->_logger->addRecord(Logger::NOTICE, $string, $context);
	}
	
	public function warning ($string, $context = [])
	{
		if ($this->_logger !== null) $this->_logger->addRecord(Logger::WARNING, $string, $context);
	}
	
	public function error ($string, $context = [])
	{
		if ($this->_logger !== null) $this->_logger->addRecord(Logger::ERROR, $string, $context);
	}
	
	public function critical ($string, $context = [])
	{
		if ($this->_logger !== null) $this->_logger->addRecord(Logger::CRITICAL, $string, $context);
	}
	
	public function alert ($string, $context = [])
	{
		if ($this->_logger !== null) $this->_logger->addRecord(Logger::ALERT, $string, $context);
	}
	
	public function emergency ($string, $context = [])
	{
		if ($this->_logger !== null) $this->_logger->addRecord(Logger::EMERGENCY, $string, $context);
	}
}
