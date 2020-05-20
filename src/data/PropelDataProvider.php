<?php
	namespace equilibrium\data;
	use Propel\Runtime\ActiveQuery\Criteria;
	use Propel\Runtime\ActiveQuery\ModelCriteria;
		
	class PropelDataProvider extends DataProvider
	{
		/**
		 * @var ModelCriteria
		 */
		protected $_query = NULL;
		
		public function __construct (ModelCriteria $q)
		{
			$this->_query = $q;
			$this->_name = ucfirst($q->getTableMap()->getName());
			$this->_columns = [];
			foreach ($this->_query->getTableMap()->getColumns() as $column)
			{
				$this->_columns[$column->getPhpName()] = ['Type' => $column->getType(), 'Description' => $column->getName(), 'Size' => $column->getSize(), 'IsPk' => $column->isPrimaryKey()];
			}
		}
		
		protected function retrieveModels()
		{
			$result = [];
			//Apply filters			
			$tmp = $this->_search;
			foreach ($tmp as $field => $value)
			{
				//if ($this->_columns[$field]['Type'] == 'VARCHAR') $tmp[$field]='%'.$value.'%';
				$method = 'filterBy'.ucfirst($field);
				if ($this->_columns[$field]['Type'] == 'VARCHAR') 
				{
					$value ='%'.$value.'%';
					$this->_query->{$method}($value, Criteria::LIKE);
				} else {
					$this->_query->{$method}($value);
				}
				
				
			}
			//$this->_query->filterByArray($tmp);
			
			foreach ($this->_sort as $field => $value)
			{
				$field = ($this->_query->getTableMap()->getColumnByPhpName($field)->getName());
				if ($value == 'ASC') $this->_query->addAscendingOrderByColumn($field);
				if ($value == 'DESC') $this->_query->addDescendingOrderByColumn($field);
			}
			
			if ($this->_pageSize != null)
			{
				$models = $this->_query->paginate($this->_page, $this->_pageSize);
				$this->_totalModels = $models->getNbResults();
				$result = $models->getResults();
				
			} else {
				$result = $this->_query->find();
				$this->_totalModels = count ($result);
			}
			return $result;
		}
	}
	