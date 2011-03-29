<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * Menu Model for Jelly ORM
 * Implements MPTT functionality
 *
 * @package Menu
 * @author  Sergei Gladkovskiy <smgladkovskiy@gmail.com>
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
					'convert_empty' => TRUE,
				)),
			    'title' => Jelly::field('String', array(
					'default' => NULL,
					'allow_null' => TRUE,
				    'convert_empty' => TRUE,
				)),
				'route_name' => Jelly::field('String', array(
					'default' => 'default'
				)),
				'directory' => Jelly::field('String', array(
					'default' => NULL,
					'allow_null' => TRUE,
					'convert_empty' => TRUE,
				)),
				'controller' => Jelly::field('String', array(
					'default' => NULL,
					'allow_null' => TRUE,
					'convert_empty' => TRUE,
				)),
				'action' => Jelly::field('String', array(
					'default' => NULL,
					'allow_null' => TRUE,
					'convert_empty' => TRUE,
				)),
				'params' => Jelly::field('Serialized', array(
					'default' => NULL,
					'allow_null' => TRUE,
					'convert_empty' => TRUE,
				)),
				'query' => Jelly::field('String', array(
					'default' => NULL,
					'allow_null' => TRUE,
					'convert_empty' => TRUE,
				)),
				'visible' => Jelly::field('Boolean', array(
					'default' => TRUE,
					'true_label' => __('виден'),
					'false_label' => __('не виден'),
				)),
				'class' => Jelly::field('String', array(
					'default' => NULL,
					'allow_null' => TRUE,
					'convert_empty' => TRUE,
				)),
			));
	    parent::initialize($meta);
	}
} // End Model_menu