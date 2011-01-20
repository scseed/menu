<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * Menu Model for Jelly ORM
 * Implements MPTT functionality
 *
 * @package Menu
 * @author  avis <smgladkovskiy@gmail.com>
 */
class Model_Menu extends Jelly_Model_MPTT {

	/**
	 * Initializating model meta information
	 *
	 * @param Jelly_Meta $meta
	 */
	public static function initialize(Jelly_Meta $meta)
	{
		$meta->table('menu')
			->fields(array(
				'id' => Jelly::field('Primary'),
				'name' => Jelly::field('String', array(
					'default' => NULL,
					'allow_null' => TRUE,
				)),
			    'title' => Jelly::field('String', array(
					'default' => NULL,
					'allow_null' => TRUE,
				)),
				'route_name' => Jelly::field('String', array(
					'default' => 'default'
				)),
				'directory' => Jelly::field('String', array(
					'default' => NULL,
					'allow_null' => TRUE,
				)),
				'controller' => Jelly::field('String', array(
					'default' => NULL,
					'allow_null' => TRUE,
				)),
				'action' => Jelly::field('String', array(
					'default' => NULL,
					'allow_null' => TRUE,
				)),
				'object_id' => Jelly::field('String', array(
					'default' => NULL,
					'allow_null' => TRUE,
				)),
				'visible' => Jelly::field('Boolean', array(
					'default' => TRUE
				)),
				'class' => Jelly::field('String'),
			));
	    parent::initialize($meta);
	}
} // End Model_menu