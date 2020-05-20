<?php
	namespace equilibrium;
	
	class Message implements IRenderizable
	{
		protected $_title = '';
		protected $_type = '';
		protected $_text = '';
		
		const TYPE_INFO = 'info';
		const TYPE_WARNING = 'warning';
		const TYPE_SUCCESS = 'success';
		const TYPE_DANGER = 'danger';

		/**
		 * Set message type
		 * @param string $type
		 * @return equilibrium\Message
		 */
		public function setType($type)
		{
			$this->_type = $type;
			return $this;
		}
		
		/**
		 * set message title
		 * @param string $title
		 * @return equilibrium\Message
		 */
		public function setTitle($title)
		{
			$this->_title = $title;
			return $this;
		}
		
		/**
		 * Set message text
		 * @param string $text
		 * @return equilibrium\Message
		 */
		public function setText ($text)
		{
			$this->_text = $text;
			return $this;
		}
		
		/**
		 * Get message text
		 * @return string
		 */
		public function getText()
		{
			return $this->_text;
		}
		
		/**
		 * Get message type
		 * @return string 
		 */
		public function getType()
		{
			return $this->_type;
		}
		
		/**
		 * Get message title
		 * @return string
		 */
		public function getTitle()
		{
			return $this->_title;
		}
		
		/**
		 * Cast to array
		 * @return array
		 */
		public function toArray()
		{
			$result = array();
			$result ['type'] = $this->getType();
			$result ['title'] = $this->getTitle();
			$result ['text'] = $this->getText();
			return $result;
		}
		
		/**
		 * Generates HTML code
		 * @return string
		 */
		public function render()
		{
			$title = '';
			if (!empty($this->getTitle())) {
				$title = '<strong>'.$this->getTitle().'</strong> <br>';
			} else {
				switch ($this->getType()) {
					case self::TYPE_DANGER:
						$title = '<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> <span class="sr-only">Error:</span>';
					break;
					
					case self::TYPE_WARNING:
						$title = '<span class="glyphicon glyphicon-warning-sign" aria-hidden="true"></span> <span class="sr-only">Atencion:</span>';
					break;
					
					case self::TYPE_SUCCESS:
						$title = '<span class="glyphicon glyphicon-ok" aria-hidden="true"></span> <span class="sr-only">Exito:</span>';
					break;
					
					default:
						$title = '<span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span> <span class="sr-only">Información:</span>';
					break;
				}
			}
			
			$out = '<div class="alert alert-'.$this->getType().' alert-dismissible" role="alert">';
			$out .= '<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>';
			$out .= $title;
			$out .= $this->getText();
			$out .= '</div>';
			return $out;
		}
	}