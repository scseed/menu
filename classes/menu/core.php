<?php defined('SYSPATH') or die('No direct access allowed.');

/**
* Class Menu
*
* @package Menu
* @author  avis <smgladkovskiy@gmail.com>
*/
abstract class Menu_Core {

	// Instance storage
	protected static $instance;

	// Menu views path
	protected $_views_path = 'menu';

	// Active menu anchor class name
	protected $_active_class = 'active';

	/**
	 * Menu instance
	 *
	 * @return object Menu
	 */
	public static function instance()
	{
		if( ! is_object(self::$instance))
		{
			self::$instance = new Menu();
		}

		return self::$instance;
	}

	/**
	 * Building menu
	 * By defaults generate two level menu
	 *
	 * @return string View
	 */
	public function generate($type = 'top')
	{
		$current_request = Request::instance();
		$current_request_params = array(
			Route::name($current_request->route),
			$current_request->directory,
			($current_request->controller == 'home') ? NULL : $current_request->controller,
			($current_request->action == 'index') ? NULL : $current_request->action,
		);
		$active_menu = implode('_', $current_request_params);

		$_menu = Jelly::query('menu')
			->where('name', '=', $type)
			->limit(1)
			->select();

		$menu_child = $_menu->children();

		$menu = NULL;
		foreach($menu_child as $child)
		{
			$key = $child->route_name . '_'
			     . $child->directory  . '_'
			     . $child->controller . '_'
			     . $child->action;
			$menu[$key]             = $child->as_array();
			$menu[$key]['parent']   = $key;
			$menu[$key]['title']    = $child->title;
			if($child->has_children())
			{
				$subchilds = $child->children();

				foreach($subchilds as $subchild)
				{
					$sub_key = $subchild->route_name . '_'
					         . $subchild->directory  . '_'
					         . $subchild->controller . '_'
					         . $subchild->action;
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

			$current_route_defaults = Request::instance()->route->get_defaults();

			if($active_menu_item === NULL)
			{
				$active_menu_item = Arr::get($current_route_defaults, 'directory', NULL)  . '_'
				                  . Arr::get($current_route_defaults, 'controller', NULL) . '_'
				                  . Arr::get($current_route_defaults, 'action', NULL)     . '_'
				                  . Arr::get($current_route_defaults, 'id', NULL);
			}

			// Marking active menu item by setting active class to it
			$menu[$active_menu_item]['class'] = $this->_active_class;
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
					'controller'    => arr::get($menu_item, 'controller', NULL),
					'action'        =>  arr::get($menu_item, 'action', NULL),
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
	 * Paren element finding and marking
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
	 * Finding current active element of menu array.
	 *
	 * @param  array $menu
	 * @return string/null
	 */
	protected function _find_current(array & $menu)
	{
		static $current;

		$href = Request::instance()->uri().URL::query();
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

	protected function _menu_fullfill()
	{
	//		$new_children = Jelly::factory('menu')->set(array(
	//			'title' => 'Партнёры',
	//			'controller' => 'partners',
	//		))->insert_as_last_child($_menu);

	//	    exit(Kohana::debug($new_children));
	//	    $new_root = Jelly::factory('menu')->set(array(
	//			'name' => 'top',
	//		    'title' => NULL,
	//		    'directory' => NULL,
	//		    'visible' => FALSE,
	//		))->insert_as_new_root();


	//		$deleted = Jelly::select('menu')->load(18)->delete_obj();
	//		$user = Jelly::select('menu')->load(14);
	//		$new_directory = Jelly::factory('menu')->set(array(
	//			'name' => 'Пользователь',
	//			'action' => NULL,
	//			'controller' => 'home',
	//			'route_name' => 'user',
	//			'visible' => 0
	//		))->insert_as_first_child($user);
	//		$im_ex = Jelly::select('menu')->load(12)->move_to_prev_sibling(10);
	}
} // End Menu_Core