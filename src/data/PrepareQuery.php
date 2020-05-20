<?php
namespace equilibrium\data;
use equilibrium\Request;

use Equilibrium;
use Propel\Runtime\ActiveQuery\ModelCriteria;

class PrepareQuery 
{
	protected $_page = 1;			//Page number
	protected $_pageSize = NULL;	//Page size
	protected $_name =  '';			//Name
	protected $_columns = [];		//Model's attributes
	protected $_search = [];		//Search filters
	protected $_sort = [];			//Sorting fields
	protected $_totalModels = null;
	
	public function __construct($name)
	{
		$this->_name = $name;
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
			foreach ($rq[$name]['Sort'] as $field => $value)
			{
				if ($value == '') continue;
				$this->_sort[$field] = $value;
			}
		}
	}
	
	/**
	 * Sets page size
	 * @param integer $v
	 * @return \equilibrium\data\PrepareQuery
	 */
	public function setPageSize($v)
	{
		$this->_pageSize = $v;
		return $this;
	}
	
	/**
	 * Gets current page size
	 * @return integer
	 */
	public function getPageSize()
	{
		return $this->_pageSize;
	}
	
	/**
	 * Sets current page
	 * @param ingeter $page
	 * @return \equilibrium\data\PrepareQuery
	 */
	public function setPage($page)
	{
		$this->_page = $page;
		return $this;
	}
	
	/**
	 * Returns current page number
	 * @return ingeter
	 */
	public function getPage()
	{
		return $this->_page;
	}
	
	/**
	 * returns procesed search filters
	 * @return array;
	 */
	public function getSearchFilters()
	{
		return $this->_search;
	}
	
	/**
	 * Returns procesed sorting preferences
	 * @return array;
	 */
	public function getSorting()
	{
		return $this->_sort;
	}
	
	/**
	 * Adds column to list
	 * @param string $name
	 * @param string $type
	 * @param string $description
	 * @param integer $size
	 * @param boolean $isPk
	 * @return \equilibrium\data\PrepareQuery
	 */
	public function addColumn($name, $type, $description, $size, $isPk)
	{
		$this->_columns[$name] = ['Type' => $type, 'Description' => $description, 'Size' => $size, 'IsPk' => $isPk];
		return $this;
	}
	
	/**
	 * Removes column from list
	 * @param string $name
	 * @return \equilibrium\data\PrepareQuery
	 */
	public function removeColumn ($name)
	{
		unset ($this->_columns[$name]);
		return $this;
	}
	
	/**
	 * Clears columns list
	 * @return \equilibrium\data\PrepareQuery
	 */
	public function clearColumns()
	{
		$this->_columns = [];
		return $this;
	}
	
	/**
	 * Returns column array
	 * @return array
	 */
	public function getColumns()
	{
		return $this->_columns;
	}
	
	
	public function getSearch()
	{
		return $this->_search;
	}
	
	public function getSort()
	{
		return $this->_sort;
	}	
	/**
	 * Sets name
	 * @param string $v
	 * @return \equilibrium\data\PrepareQuery
	 */
	public function setName($v)
	{
		$this->_name = $v;
		return $this;
	}
	
	/**
	 * Gets name
	 * @param string $v
	 * @return string
	 */
	public function getName()
	{
		return $this->_name;
	}
	
	/**
	 * Applies filtering and sorting to a query
	 * @param ModelCriteria $q
	 */
	public function prepare(ModelCriteria & $q)
	{
		//Apply filters
		$tmp = $this->_search;
		foreach ($tmp as $field => $value)
		{
			if ($this->_columns[$field]['Type'] == 'VARCHAR') $tmp[$field]='%'.$value.'%';
		}
		$q->filterByArray($tmp);
		
		//Apply sorting
		foreach ($this->_sort as $field => $value)
		{
			$field = ($q->getTableMap()->getColumnByPhpName($field)->getName());
			if ($value == 'ASC') $q->addAscendingOrderByColumn($field);
			if ($value == 'DESC') $q->addDescendingOrderByColumn($field);
		}
	}
	
	public function getTotalModels()
	{
		return $this->_totalModels;
	}
	
	public function getTotalPages()
	{
		if ($this->_pageSize == null) return 1;
		return ceil($this->getTotalModels() / $this->getPageSize());
	}
	
	/**
	 * Applies pagination (if required) and retrieve models
	 * @param ModelCriteria $q
	 * @return ObjectCollection|ActiveRecordInterface[]|array|mixed the list of results, formatted by the current formatter
	 */
	public function getModels(ModelCriteria & $q)
	{
		$result = [];
		//Apply pagination
		if ($this->_pageSize != null)
		{
			$models = $q->paginate($this->_page, $this->_pageSize);
			$this->_totalModels = $models->getNbResults();
			$result = $models->getResults();
		
		} else {
			$result = $q->find();
			$this->_totalModels = count ($result);
		}
		return $result;
	}
}
