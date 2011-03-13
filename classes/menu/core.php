<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * Class Menu
 *
 * @package Menu
 * @author  Sergei Gladkovskiy <smgladkovskiy@gmail.com>
 */
abstract class Menu_Core {

	/**
	 * Instance storage
	 *
	 * @var array
	 */
	protected static $instances;

	/**
	 * Menu views path
	 *
	 * @var string
	 */
	protected $_views_path = 'menu';

	/**
	 * Active menu anchor class name
	 *
	 * @var string
	 */
	protected $_active_class = 'active';

	/**
	 * @var string
	 */
	protected $_destination;

	/**
	 * Menu instance
	 *
	 * @return object Menu
	 */
	public static function factory($destination = 'default')
	{
		$menu_extention = 'Menu_' . ucfirst($destination);

		if(class_exists($menu_extention))
		{
			$menu_class = $menu_extention;
		}
		else
		{
			$menu_class = 'Menu';
		}

		if( self::$instances[$destination] === NULL)
		{
			self::$instances[$destination] = new $menu_class($destination);
		}

		return self::$instances[$destination];
	}

	/**
	 * @param string $destination
	 */
	public function __construct($destination)
	{
		$this->_destination = $destination;
	}

	/**
	 * Building menu
	 * By defaults generate two level menu
	 *
	 * @return string View
	 */
	public function generate($type = 'top')
	{
//		return $this->_menu_fullfill();

		$current_request = Request::current();
		$current_request_params = array(
			Route::name($current_request->route()),
			$current_request->directory(),
			$current_request->controller(),
			$current_request->action(),
//			serialize($current_request->param()),
		);

		$active_menu = implode('_', $current_request_params);

		$_menu = $this->_get_root($type);

		$menu_child = $_menu->children();

		$menu = array();
		foreach($menu_child as $child)
		{
			$route          = Route::get($child->route_name);
			$route_defaults = $route->get_defaults();

			$directory  = ($child->directory)  ? $child->directory  : Arr::get($route_defaults, 'directory', NULL);
			$controller = ($child->controller) ? $child->controller : $route_defaults['controller'];
			$action     = ($child->action)     ? $child->action     : $route_defaults['action'];
			$key = $child->route_name . '_'
			     . $directory         . '_'
			     . $controller        . '_'
			     . $action;
			$menu[$key]             = $child->as_array();
			$menu[$key]['parent']   = $key;
			$menu[$key]['title']    = $child->title;
			if($child->has_children())
			{
				$subchilds = $child->children();

				foreach($subchilds as $subchild)
				{
					$route          = Route::get($subchild->route_name);
					$route_defaults = $route->get_defaults();

					$directory  = ($subchild->directory)  ? $subchild->directory  : Arr::get($route_defaults, 'directory', NULL);
					$controller = ($subchild->controller) ? $subchild->controller : $route_defaults['controller'];
					$action     = ($subchild->action)     ? $subchild->action     : $route_defaults['action'];
					$sub_key = $subchild->route_name . '_'
					     . $directory                . '_'
					     . $controller               . '_'
					     . $action;
					$menu[$key]['submenu'][$sub_key]            = $subchild->as_array();
					$menu[$key]['submenu'][$sub_key]['parent']  = $key;
					$menu[$key]['submenu'][$sub_key]['title']   = $subchild->title;;
				}
			}
		}

		if($menu)
		{
			// Forming menu array from database data
			$menu = $this->_gen_menu($menu);


			// Searching the active menu item
			$active_menu_item    = $this->_find_parent($menu, $active_menu);
			$active_submenu_item = $this->_find_current($menu);

			// Marking active menu item by setting active class to it
			$menu_item_to_set_active = Arr::get($menu, $active_menu_item);
			if($menu_item_to_set_active)
			{
				$menu[$active_menu_item]['class'] = $this->_active_class;
			}

			if($active_submenu_item)
				$menu[$active_menu_item]['submenu'][$active_submenu_item]['class'] = $this->_active_class;

			return View::factory($this->_views_path . DIRECTORY_SEPARATOR . $type)->bind('menu_arr', $menu);
		}
		else
		{
			return NULL;
		}
	}

	/**
	 * Menu array generating to pass it to a view generating.
	 * Fills empty values to preserve errors.
	 *
	 * @param  array  $menu_array
	 * @param  string $parent
	 * @return array  $menu
	 */
	protected function _gen_menu(array $menu_array, $parent = NULL)
	{
		$menu = array();
		foreach($menu_array as $item_name => $menu_item)
		{
			$route_name     = arr::get($menu_item, 'route_name', 'default');
			$route          = Route::get($route_name);
			$route_defaults = $route->get_defaults();

			if($menu_item['route_name'] == 'page')
			{
				$href = $route->uri(array(
					'lang'       => I18n::$lang,
					'page_alias' => arr::get($menu_item, 'object_id', NULL),
				));
			}
			else
			{
				$href = $route->uri(array(
					'directory'     => arr::get($menu_item, 'directory', NULL),
					'controller'    => arr::get($menu_item, 'controller', $route_defaults['controller']),
					'action'        => arr::get($menu_item, 'action', $route_defaults['action']),
					'id'            => arr::get($menu_item, 'object_id', NULL),
				));
			}

			if( ! $this->_access_check(
				$route_name,
				arr::get($menu_item, 'controller', $route_defaults['controller'])
			))
			{
				continue;
			}

			if($parent === NULL)
			{
				$parent_name = $item_name;
			}
			else
			{
				$parent_name = $parent;
			}

			$menu[$item_name] = array(
				'parent'    => $parent_name,                                            // parent lavel name
				'title'     => __(arr::get($menu_item, 'title', '')),                   // anchor title
				'href'      => $href,                                                   // anchor href
				'class'     => arr::get($menu_item, 'class', NULL),                     // anchor class name
				'directory' => $route_name,                                             // route directory
				'visible'   => arr::get($menu_item, 'visible', TRUE),                   // anchor visibility
				'submenu'   => ( ! empty($menu_item['submenu']))                        // submenu
				                ? $this->_gen_menu($menu_item['submenu'], $parent_name)
				                : array(),
			);

		}

		return $menu;
	}

	/**
	 * Finds parent element and marks it
	 *
	 * @param  array  $menu_array
	 * @param  string $active_menu
	 * @return string $parent
	 */
	protected function _find_parent(array & $menu_array, $active_menu = NULL)
	{
		static $parent;

		foreach($menu_array as $name => $item)
		{
			if ($name == $active_menu)
			{
				$parent = $item['parent'];
			}

			if(! empty($item['submenu']))
			{
				$parent = $this->_find_parent($item['submenu'], $active_menu);
			}
		}

		if($parent) return $parent;

		return NULL;
	}

	/**
	 * Finds current active element of menu array.
	 *
	 * @param  array $menu
	 * @return string/null
	 */
	protected function _find_current(array & $menu)
	{
		static $current;

		$href = Request::current()->uri().URL::query();
		foreach($menu as $name => $item)
		{
			if($item['href'] == $href AND $name != $item['parent'])
			{
				$current = $name;
			}

			if(! empty($item['submenu']))
			{
				$current = $this->_find_current($item['submenu']);
			}
		}

		if($current) return $current;

		return NULL;
	}

	/**
	 * Access check
	 *
	 * @param  string $route_name
	 * @param  string $controller
	 * @return bool
	 */
	protected function _access_check($route_name, $controller)
	{
//		if(class_exists('ACL'))
//		{
//			if ( ! ACL::instance()->is_allowed(
//				Auth::instance()->get_user()->roles->as_array('id', 'name'),
//				array(
//					'route_name' => $route_name,
//					'resource' => $controller
//				),
//				array('read')))
//			{
//				return FALSE;
//			}
//		}
//
		return TRUE;
	}

	/**
	 * Finds menu root
	 *
	 * @param  string $name
	 * @return Jelly_Model_MPTT
	 */
	protected function _get_root($name)
	{
		return Jelly::query('menu')
			->where('name', '=', $name)
			->where('title', '=', NULL)
			->where('route_name', '=', $this->_destination)
			->limit(1)
			->select();
	}
} // End Menu_Core