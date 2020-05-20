<?php
	namespace equilibrium;
	
	class Session
	{
		protected $prefix = '';
		
		public function __construct($prefix = 'APP')
		{
			@session_start();
			if (!empty ($prefix)) $this->prefix = $prefix.'__';
		}
		
		public function __get($field)
		{
			return $_SESSION[$this->prefix . $field];
		}
		
		public function __set($field, $value)
		{
			$_SESSION[$this->prefix . $field] = $value;
		}
		
		public function __isset($field)
		{
			return isset($_SESSION[$this->prefix . $field]);
		}
		
		public function __unset($field)
		{
			unset ($_SESSION[$this->prefix . $field]);
		}
		
		public function exists($field)
		{
			return isset($this->{$field});
		}
		
		public function erase($field)
		{
			unset ($this->{$field});
		}
		
		public function clear()
		{
			//TODO: Borrar solamente los datos con el prefijo
			$_SESSION = [];
		}
		
		public function resetSession()
		{
			$this->clear();
			session_regenerate_id(true);
			session_destroy();
			session_start();
		}
	}