<?php
	namespace equilibrium;
	use Equilibrium;
	use equilibrium\exceptions\NotFoundException;
	use equilibrium\exceptions\CSRFCheckException;
		
	class Request
	{
		protected $_request = [];
		protected $_objectRequest = null;
		protected $_throwException = false;
		
		/**
		 * Constructor 
		 * @param array $requestSource ($_GET, $_POST, $_REQUEST, $_COOKIE...)
		 * @param bool $checkCSRF (If true, perform a CSRF check)
		 * @param string[1] $splitChar (Character used to convert request source to nested array)
		 */
		
		public function __construct($requestSource, $checkCSRF = true, $splitChar = null)
		{
			if ($splitChar == null) 
			{
				$this->_request = $requestSource;
				
			} else {
				foreach ($requestSource as $key=>$value)
				{
					$path = explode($splitChar,$key);
					$temp = &$this->_request;
					foreach ( $path as $mkey ) {
						$temp = &$temp[$mkey];
					}
					$temp = $value;
				}
			}
			
			if (!empty($this->_request) && $checkCSRF)
			{
				$token = Equilibrium::getCSRFToken();
				if (! isset($this->_request[$token['name']]) || ($this->_request[$token['name']] != $token['value'])  )
					throw new CSRFCheckException();
			}
			
			$this->_objectRequest = Equilibrium::arrayToObject($this->_request);
		}
		
		public function asArray()
		{
			return $this->_request;
		}
		
		public function __get($field)
		{
			if ($this->_throwException && !isset($this->_objectRequest->{$field})) throw new NotFoundException();
			if (!$this->_throwException && !isset($this->_objectRequest->{$field})) return null;
			return $this->_objectRequest->{$field};
		}
		
		public function __isset($field)
		{
			return isset($this->_objectRequest->{$field});
		}
		
		public function isEmpty()
		{
			return (empty($this->_request));
		}
		
	}
