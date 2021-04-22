<?php
	namespace equilibrium\data;
	use Equilibrium;
	use equilibrium\Request;
		
	abstract class DataProvider
	{
		protected $_page = 1;			//Page number
		protected $_pageSize = NULL;	//Page size
		protected $_name =  '';			//Name
		protected $_search = [];		//Search filters
		protected $_columns = [];		//Model's attributes
		protected $_totalModels = 0;	//Ammount of models without pagination	
		protected $_sort = [];			//Sorting fields
		
		public function setDefaultSortOrder($field, $criteria = 'ASC')
		{
			$this->_sort[$field] = $criteria;
		}
		
		public function load(Request $request = null)
		{
			$name = $this->_name;
			if ($request == null) $request = Equilibrium::post();
			$rq = $request->asArray();
			$this->_page = (isset($rq[$name]['Page'])&&($rq[$name]['Page'] != ''))?$rq[$name]['Page']:$this->_page;
			if (isset($rq[$name]['Search']) && is_array($rq[$name]['Search']))
			{
				foreach ($rq[$name]['Search'] as $field => $value)
				{
					if ($value == '') continue;
					$this->_search[$field] = $value;
				}
			}
			if (isset($rq[$name]['Sort']) && is_array($rq[$name]['Sort']))
			{
				$this->_sort = [];
				foreach ($rq[$name]['Sort'] as $field => $value)
				{
					if ($value == '') continue;
					$this->_sort[$field] = $value;
				}
			}
		}
		
		/**
		 * Gets data provider name
		 * @return string;
		 */
		public function getName()
		{
			return $this->_name;
		}
		
		/**
		 * Sets data provider name
		 * @param string $v
		 * @return DataProvider
		 */
		public function setName ($v)
		{
			$this->_name = $v;
			return $this;
		}
		
		/**
		 * Gets items per page
		 * @return integer;
		 */
		public function getPageSize()
		{
			return $this->_pageSize;
		}
		
		/**
		 * Sets items per page
		 * @param integer $v
		 * @return DataProvider
		 */
		public function setPageSize($v)
		{
			$this->_pageSize = $v;
			return $this;
		}
		
		/**
		 * Gets items page number
		 * @return integer
		 */
		public function getPage()
		{
			return $this->_page;
		}
		
		/**
		 * Sets items page number
		 * @param integer $v
		 * @return DataProvider
		 */
		public function setPage($v)
		{
			$this->_page = $v;
			return $this;
		}

		public function getModels()
		{
			$models = $this->retrieveModels();
			$result = ['Columns' => $this->_columns, 
						'Models' => $models, 
						'Page' => $this->_page,
						'Pages' => ($this->_pageSize > 0)?max(ceil($this->_totalModels/$this->_pageSize),1):0,
						'PageSize' => $this->_pageSize, 
						'Name' => $this->_name, 
						'TotalModels' => $this->_totalModels, 
						'Search' => $this->_search,
						'Sort' => $this->_sort,
					
			];
			return $result;
		}

		abstract protected function retrieveModels();
	}