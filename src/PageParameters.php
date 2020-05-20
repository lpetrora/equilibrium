<?php
	namespace equilibrium;
	use equilibrium\Session;
	
	class PageParameters
	{
		protected $_pp = [];
		protected static $_session = null;

		public function __set($field, $value)
		{
			$this->_pp[$field] = $value;
		}
		
		public function __get($field)
		{
			return $this->_pp[$field];
		}
		
		public function getParameters()
		{
			return $this->_pp;
		}
		
		protected static function getSessionHandler()
		{
			if (static::$_session == null) static::$_session = new Session('PageParameteres');
			return static::$_session;
		}
		
		/**
		 * Registra un PageParameters a nombre de una clase
		 * @param string $class
		 * @param PageParameters $pp
		 */
		static public function registerPageParemeters($class, PageParameters $pp)
		{
			$session = self::getSessionHandler();
			$session->{$class} = $pp;
		}
		
		/**
		 * Obtiene el PageParemeters del controlador
		 * @param Controller $object
		 * @return mixed (null or PageParameters)
		 */
		static public function retrievePageParameters(Controller $object)
		{
			$session = self::getSessionHandler();
			$result = null;
			$class = get_class($object);
			if (isset($session->{$class}))
			{
				$result = $session->{$class};
				unset($session->{$class});
			}
			return $result;
		}
	}