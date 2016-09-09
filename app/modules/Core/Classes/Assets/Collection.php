<?php

namespace Sky\Core\Assets;

/**
 * @author AlexPro
 * @copyright 2008 - 2016 XNova Game Group, Olympia.Digital
 * Telegram: @alexprowars, Skype: alexprowars, Email: alexprowars@gmail.com
 */

use Phalcon\Assets\Exception;
use Phalcon\Assets\Resource;
use Phalcon\Assets\Resource\Css;
use Phalcon\Assets\Resource\Js;

class Collection
{
	/**
	 * @var null|string
	 * @access protected
	 */
	protected $_prefix;

	/**
	 * @var boolean
	 * @access protected
	 */
	protected $_local;

	/**
	 * @var null|array
	 * @access protected
	 */
	protected $_resources;

	/**
	 * @var null|int
	 * @access protected
	 */
	protected $_position;

	/**
	 * @var null|array
	 * @access protected
	 */
	protected $_filters;

	/**
	 * @var null|array
	 * @access protected
	 */
	protected $_attributes;

	/**
	 * @var boolean
	 * @access protected
	 */
	protected $_join = true;

	/**
	 * @var null|string
	 * @access protected
	 */
	protected $_targetUri;

	/**
	 * @var null|string
	 * @access protected
	 */
	protected $_targetPath;

	/**
	 * @var null|string
	 * @access protected
	 */
	protected $_sourcePath;
	protected $_targetLocal = true;

	/**
	 * Adds a resource to the collection
	 *
	 * @param \Phalcon\Assets\Resource $resource
	 * @return Collection
	 * @throws Exception
	 */
	public function add($resource)
	{
		if (!is_object($resource) || $resource instanceof Resource === false)
			throw new Exception('Resource must be an object');

		$this->_resources[] = $resource;

		return $this;
	}

	/**
	 * Adds a CSS resource to the collection
	 *
	 * @param string $path
	 * @param boolean|null $local
	 * @param boolean|null $filter
	 * @param array|null $attributes
	 * @return Collection
	 * @throws Exception
	 */
	public function addCss($path, $local = null, $filter = null, $attributes = null)
	{
		if (!is_string($path))
			throw new Exception('Invalid parameter type.');

		if (!is_null($filter))
			$filter = true;
		elseif (!is_bool($filter))
			throw new Exception('Invalid parameter type.');

		if (is_array($this->_resources) === false)
			$this->_resources = [];

		$this->_resources[] = new Css($path, (is_bool($local) === true ? $local : $this->_local), $filter, (is_array($attributes) === true ? $attributes : $this->_attributes));

		return $this;
	}

	/**
	 * Adds a javascript resource to the collection
	 *
	 * @param string $path
	 * @param boolean|null $local
	 * @param boolean|null $filter
	 * @param array|null $attributes
	 * @return Collection
	 * @throws Exception
	 */
	public function addJs($path, $local = null, $filter = null, $attributes = null)
	{
		if (!is_string($path))
			throw new Exception('Invalid parameter type.');

		if (!is_null($filter))
			$filter = true;
		elseif (!is_bool($filter))
			throw new Exception('Invalid parameter type.');

		if (!is_array($this->_resources))
			$this->_resources = [];

		$this->_resources[] = new Js($path, (is_bool($local) === true ? $local : $this->_local), $filter, (is_array($attributes) === true ? $attributes : $this->_attributes));

		return $this;
	}

	/**
	 * Returns the resources as an array
	 *
	 * @return \Phalcon\Assets\Resource[]
	 */
	public function getResources()
	{
		return (is_array($this->_resources) === true ? $this->_resources : []);
	}

	/**
	 * Returns the number of elements in the form
	 *
	 * @return int
	 */
	public function count()
	{
		if (!is_array($this->_resources))
			$this->_resources = [];

		return count($this->_resources);
	}

	/**
	 * Rewinds the internal iterator
	 */
	public function rewind()
	{
		$this->_position = 0;
	}

	/**
	 * Returns the current resource in the iterator
	 *
	 * @return \Phalcon\Assets\Resource|null
	 */
	public function current()
	{
		if (!is_array($this->_resources))
			$this->_resources = [];

		if (!is_int($this->_position))
			$this->_position = 0;

		return (isset($this->_resources[$this->_position]) ? $this->_resources[$this->_position] : null);
	}

	/**
	 * Returns the current position/key in the iterator
	 *
	 * @return int
	 */
	public function key()
	{
		if (!is_int($this->_position))
			$this->_position = 0;

		return $this->_position;
	}

	/**
	 * Moves the internal iteration pointer to the next position
	 *
	 */
	public function next()
	{
		if (!is_int($this->_position))
			$this->_position = 0;

		$this->_position++;
	}

	/**
	 * Check if the current element in the iterator is valid
	 *
	 * @return boolean
	 */
	public function valid()
	{
		if (!is_int($this->_position))
			$this->_position = 0;

		if (!is_array($this->_resources))
			$this->_resources = [];

		return isset($this->_resources[$this->_position]);
	}

	/**
	 * Sets the target path of the file for the filtered/join output
	 *
	 * @param string $targetPath
	 * @return Collection
	 * @throws Exception
	 */
	public function setTargetPath($targetPath)
	{
		if (!is_string($targetPath))
			throw new Exception('Invalid parameter type.');

		$this->_targetPath = $targetPath;

		return $this;
	}

	/**
	 * Returns the target path of the file for the filtered/join output
	 *
	 * @return string|null
	 */
	public function getTargetPath()
	{
		return $this->_targetPath;
	}

	/**
	 * Sets a base source path for all the resources in this collection
	 *
	 * @param string $sourcePath
	 * @return Collection
	 * @throws Exception
	 */
	public function setSourcePath($sourcePath)
	{
		if (!is_string($sourcePath))
			throw new Exception('Invalid parameter type.');

		$this->_sourcePath = $sourcePath;

		return $this;
	}

	/**
	 * Returns the base source path for all the resources in this collection
	 *
	 * @return string|null
	 */
	public function getSourcePath()
	{
		return $this->_sourcePath;
	}

	/**
	 * Sets a target uri for the generated HTML
	 *
	 * @param string $targetUri
	 * @return Collection
	 * @throws Exception
	 */
	public function setTargetUri($targetUri)
	{
		if (!is_string($targetUri))
			throw new Exception('Invalid parameter type.');

		$this->_targetUri = $targetUri;

		return $this;
	}

	/**
	 * Returns the target uri for the generated HTML
	 *
	 * @return string|null
	 */
	public function getTargetUri()
	{
		return $this->_targetUri;
	}

	/**
	 * Sets a common prefix for all the resources
	 *
	 * @param string $prefix
	 * @return Collection
	 * @throws Exception
	 */
	public function setPrefix($prefix)
	{
		if (!is_string($prefix))
			throw new Exception('Invalid parameter type.');

		$this->_prefix = $prefix;

		return $this;
	}

	/**
	 * Returns the prefix
	 *
	 * @return string|null
	 */
	public function getPrefix()
	{
		return $this->_prefix;
	}

	/**
	 * Sets if the collection uses local resources by default
	 *
	 * @param boolean $local
	 * @return Collection
	 * @throws Exception
	 */
	public function setLocal($local)
	{
		if (!is_bool($local))
			throw new Exception('Invalid parameter type.');

		$this->_local = $local;

		return $this;
	}

	/**
	 * Returns if the collection uses local resources by default
	 *
	 * @return boolean
	 */
	public function getLocal()
	{
		return $this->_local;
	}

	/**
	 * @param boolean $targetLocal
	 * @return Collection
	 * @throws Exception
	 */
	public function setTargetLocal($targetLocal)
	{
		if (!is_bool($targetLocal))
			throw new Exception('Invalid parameter type.');

		$this->_targetLocal = $targetLocal;

		return $this;
	}

	/**
	 * @return boolean
	 */
	public function getTargetLocal()
	{
		return $this->_targetLocal;
	}

	/**
	 * Sets extra HTML attributes
	 *
	 * @param array $attributes
	 * @return $this
	 * @throws Exception
	 */
	public function setAttributes($attributes)
	{
		if (!is_array($attributes))
			throw new Exception('Attributes must be an array');

		if (!is_array($this->_attributes))
			$this->_attributes = [];

		$this->_attributes = $attributes;

		return $this;
	}

	/**
	 * Returns extra HTML attributes
	 *
	 * @return array|null
	 */
	public function getAttributes()
	{
		return $this->_attributes;
	}

	/**
	 * Adds a filter to the collection
	 *
	 * @param FilterInterface $filter
	 * @return Collection
	 * @throws Exception
	 */
	public function addFilter($filter)
	{
		if (!is_object($filter) || $filter instanceof FilterInterface === false)
			throw new Exception('Invalid parameter type.');

		if (!is_array($this->_filters))
			$this->_filters = [];

		$this->_filters[] = $filter;

		return $this;
	}

	/**
	 * Sets an array of filters in the collection
	 *
	 * @param array $filters
	 * @return Collection
	 * @throws Exception
	 */
	public function setFilters($filters)
	{
		if (!is_array($filters))
			throw new Exception('Filters must be an array of filters');

		if (!is_array($this->_filters))
			$this->_filters = [];

		$this->_filters = $filters;

		return $this;
	}

	/**
	 * Returns the filters set in the collection
	 *
	 * @return array|null
	 */
	public function getFilters()
	{
		return $this->_filters;
	}

	/**
	 * Sets if all filtered resources in the collection must be joined in a single result file
	 *
	 * @param boolean $join
	 * @return Collection
	 * @throws Exception
	 */
	public function join($join)
	{
		if (!is_bool($join))
			throw new Exception('Invalid parameter type.');

		$this->_join = $join;

		return $this;
	}

	/**
	 * Returns if all the filtered resources must be joined
	 *
	 * @return boolean
	 */
	public function getJoin()
	{
		return $this->_join;
	}

	/**
	 * Returns the complete location where the joined/filtered collection must be written
	 *
	 * @param string $basePath
	 * @return string
	 * @throws Exception
	 */
	public function getRealTargetPath($basePath = null)
	{
		if (!is_null($basePath))
			$basePath = '';
		elseif (!is_string($basePath))
			throw new Exception('Invalid parameter type.');

		$completePath = $basePath.$this->_targetPath;

		if (!file_exists($completePath))
			return realpath($completePath);

		return $completePath;
	}
}