<?php 
namespace equilibrium;

use equilibrium\exceptions\FileErrorException;

class File
{
	protected $_path = '';
	protected $_name = '';
	protected $_type = '';
	protected $_error = '';
	protected $_extension = '';
	
	protected $_ready = false;
	protected $_fromUpload = false;
	
	
	/**
	 * Bind to uploaded file
	 * @param array $record
	 * @return \equilibrium\File
	 */
	static public function fromUploadedFile($record)
	{
		$file = new File();
		$file->bindToUploadedFile($record);
		return $file;
	}
	
	/**
	 * Open an existing file
	 * @param string $filepath
	 * @return \equilibrium\File
	 */
	static public function fromExistentFile($filepath)
	{
		$file = new File();
		$file->open($filepath);
		return $file;
	}
	
	/**
	 * Create a new file
	 * @param string $filepath
	 * @return \equilibrium\File
	 */
	static public function fromNewFile($filepath)
	{
		$file = new File();
		$file->create($filepath);
		return $file;
	}
	
	public function __destruct()
	{
		if ($this->_fromUpload && file_exists($this->_path)) {
			@unlink($this->_path);
		}
	}
	
	public function bindToUploadedFile($record)
	{
		$tmp = '';
		if (strpos($record['name'], '.') !== false)
		{
			$tmp = explode('.', $record['name']);
			$tmp = end($tmp);
		}
		$this->_path = $record['tmp_name'];
		$this->_extension = $tmp;
		$this->_name = $record['name'];
		$this->_type = $record['type'];
		$this->_error = $record['error'];
		$this->_ready = true;
		$this->_fromUpload = true;
	}
	
	/**
	 * Creates an empty file
	 * @param string $filepath
	 * @throws FileErrorException
	 */
	public function create ($filepath)
	{
		$fp = fopen($filepath, 'w+');
		if ($fp !== false)
		{
			fclose ($fp);
			$this->_path = $filepath;
			$this->_ready = false;
			$this->_fromUpload = false;
		} else {
			throw new FileErrorException('Unable to create file ' . $filepath);
		}
	}
	
	/**
	 * Handle an existing file
	 * @param string $filepath
	 */
	public function open($filepath)
	{
		if (is_readable($filepath))
		{
			$this->_path = $filepath;
			$this->_ready = false;
			$this->_fromUpload = false;
		} else {
			throw new FileErrorException('Unable to open file ' . $filepath);
		}
	}
	
	/**
	 * Truncates file to 0 bytes
	 */
	public function trunc()
	{
		$this->create($this->_path);
	}

	/**
	 * Get file contents
	 * @return string
	 */
	public function getContents()
	{
		return file_get_contents($this->_path);
	}
	
	/**
	 * Truncates file and writes $contents. Returns bytes written
	 * @param string $contents
	 * @return number
	 */
	public function putContents($contents)
	{
		return file_put_contents($this->_path, $contents);
	}
	
	public function move($filepath)
	{
		if( rename ($this->_path, $filepath))
		{
			$this->_path = $filepath;
			$this->_ready = false;
			$this->_fromUpload = false;
			return true;
		}
		return false;
	}
	
	public function getFilename()
	{
		if (!$this->_ready) $this->readFileParameters();
		return $this->_name;
	}
	
	public function getExtension()
	{
		if (!$this->_ready) $this->readFileParameters();
		return $this->_extension;
	}
	
	public function getType()
	{
		if (!$this->_ready) $this->readFileParameters();
		return $this->_type;
	}
	
	public function getSize()
	{
		return filesize($this->_path);
	}
	
	public function getError()
	{
		return ($this->_error);
	}
	
	public function getPath()
	{
		return ($this->_path);
	}
	
	protected function readFileParameters()
	{
		$parts = pathinfo($this->_path);
		$this->_name = $parts['basename'];
		$this->_extension = isset($parts['extension'])?$parts['extension']:'';
		$parts = finfo_open(FILEINFO_MIME_TYPE);
		$this->_type = finfo_file($parts, $this->_path);
		$this->_ready = true;
	}
	
}