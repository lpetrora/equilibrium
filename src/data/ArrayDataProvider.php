<?php
	namespace equilibrium\data;
	use equilibrium\data\DataProvider;
		
	class ArrayDataProvider extends DataProvider
	{
		const Integer = 'INTEGER';
		const String = 'VARCHAR';
		
		protected $_models = [];
		
		public function __construct($models)
		{
			$this->_models = $models;
			$this->_totalModels = count ($models);
		}
		
		/**
		 * Sets total quantity of models without pagination
		 * @param integer $v
		 * @return ArrayDataProvider
		 */
		public function setTotalModels($v)
		{
			$this->_totalModels = $v;
			return $this;
		}
		
		/**
		 * Adds a column to the collection
		 * @param string $name
		 * @param string $description
		 * @param string $type
		 * @param integer $size
		 * @return ArrayDataProvider
		 */
		public function addColumn ($name, $description, $type, $size = 10)
		{
			$this->_columns[$name] = ['Type' => $type, 'Description' => $description, 'Size' => $size];
			return $this;
		}
		
		protected function retrieveModels()
		{
			return $this->_models;
		}
	}
