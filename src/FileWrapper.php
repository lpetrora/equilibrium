<?php
namespace equilibrium;

use equilibrium\File;
use Equilibrium;
use equilibrium\exceptions\NotFoundException;

class FileWrapper
{
	protected $_files = null;
	protected $_arrFiles = [];
	protected $_counter = 0;
	
	public function __construct($splitChar = null)
	{
		$result = [];
		foreach ($_FILES as $name => $file)
		{
			if (is_array($file['error']))
			{
				foreach ($file['error'] as $index=>$void)
				{
					$tmp = ['name' => $file['name'][$index],
							'type' => $file['type'][$index],
							'tmp_name' => $file['tmp_name'][$index],
							'error' => $file['error'][$index],
							'size' => $file['size'][$index]
					];
					if ($tmp['error'] != UPLOAD_ERR_NO_FILE) 
					{
						$result[$name][] = File::fromUploadedFile($tmp);
						$this->_counter++;
					}
				}
			} else {
				if ($file['error'] != UPLOAD_ERR_NO_FILE) {
					$result[$name] = File::fromUploadedFile($file);
					$this->_counter++;
				}
			}
		}
		
		if ($splitChar !== null)
		{
			$tmp = [];
			foreach ($result as $key => $value)
			{
				$path = explode($splitChar,$key);
				$temp = &$tmp;
				foreach ( $path as $mkey ) {
					$temp = &$temp[$mkey];
				}
				$temp = $value;
			}
			$result = $tmp;
		}

		$this->_arrFiles = $result;
		$this->_files = Equilibrium::arrayToObject($result);
	}
	
	public function count()
	{
		return $this->_counter;
	}
	
	public function isEmpty()
	{
		return ($this->count() == 0);
	}
	
	public function __isset($field)
	{
		return isset($this->_files->{$field});
	}
	
	public function __get($field)
	{
		if (!isset($this->_files->{$field})) throw new NotFoundException($field);
		return $this->_files->{$field};
	}
	
	public function asArray()
	{
		return $this->_arrFiles;
	}
}