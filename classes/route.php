<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Route extention
 *
 * @package Menu
 */
class Route extends Kohana_Route {

	/**
	 * Extracting default params for route
	 *
	 * @return array
	 */
	public function get_defaults()
	{
		return $this->_defaults;
	}
}
