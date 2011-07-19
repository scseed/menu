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
	 * Menu views path
	 *
	 * @var string
	 */
	protected $_lang_icons_path = 'i/icons/';

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
	 * @static
	 * @param string $destination
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

		if( Arr::get(self::$instances, $destination, NULL) === NULL)
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

	public function lang()
	{
		if(class_exists('Page'))
		{
			$languages = Page::instance()->system_langs_object();
		}
		else
		{
			$languages = Jelly::query('system_lang')->active()->select();
		}

		$current_lang = I18n::lang();
		$icons = NULL;

		foreach($languages as $language)
		{
			$icons[$language->abbr] = $this->_lang_icons_path.$language->abbr.'.png';
		}

		return View::factory($this->_views_path.DIRECTORY_SEPARATOR.'lang')
			->bind('languages', $languages)
			->bind('icons', $icons)
			->bind('current_lang', $current_lang);
	}

	/**
	 * Building menu
	 * By defaults generate two level menu
	 *
	 * @param string $type
	 * @return Kohana_View|null
	 */
	public function generate($type = 'top')
	{
		$current_request = Request::current();
		$_params = $current_request->param();

		// Ignore Lang param
		unset($_params['lang']);
		if((int) Arr::get($_params, 'id', NULL) > 0)
			unset($_params['id']);

		$_params = ($_params) ? serialize($_params) : '';
		$query = preg_replace('/lang=([a-z]{2})/', NULL, URL::query());
		$query = ($query == '?') ? NULL : $query;
		
		$current_request_params = array(
			Route::name($current_request->route()),
			$current_request->directory(),
			$current_request->controller(),
			$current_request->action(),
			$_params,
			$query,
		);

		$active_menu = implode('_', $current_request_params);

		// Overriding page->home to home->index path
		if($active_menu == 'page__page_show_a:1:{s:9:"page_path";s:4:"home";}_')
			$active_menu = 'default__home_index__';

		$menu = Page::instance()->pages_structure();

		if($menu)
		{
			// Forming menu array from database data
			$menu = $this->_gen_menu($menu);

			// Searching the active menu item
			$active_menu_item    = $this->_find_parent($menu, $active_menu);
			$active_submenu_item = $this->_find_current($menu);

			// Marking active menu item by setting active class to it
			if(Arr::get($menu, $active_menu_item, FALSE))
			{
				$menu[$active_menu_item]['active_class'] = $this->_active_class;
			}

			if($active_submenu_item AND $active_submenu_item != $active_menu_item)
				$menu[$active_menu_item]['childrens'][$active_submenu_item]['active_class'] = $this->_active_class;

			$menu = $this->_clear_hidden($menu);

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
		foreach($menu_array as $id => $menu_item)
		{
			$item_name      = $menu_item['key'];
			$route_name     = Arr::get($menu_item, 'route_name', 'default');
			$route          = Route::get($route_name);
			$route_defaults = $route->get_defaults();

			$host = Arr::get($route_defaults, 'host', FALSE);
			$config = Kohana::config('pages');

			$lang = NULL;

			if($config->multilanguage === TRUE)
			{
				$lang = i18n::lang();
			}

			$params = array(
				'lang'          => Arr::get($menu_item, 'lang', $lang),
				'directory'     => Arr::get($menu_item, 'directory', NULL),
				'controller'    => Arr::get($menu_item, 'controller', NULL),
				'action'        => Arr::get($menu_item, 'action', NULL),
			);

			$params += unserialize(Arr::get($menu_item, 'params', array()));

			$href = ($host === FALSE)
				? $route->uri($params) . Arr::get($menu_item, 'query', NULL)
				: $host;

			if( ! $this->_access_check($route_name,	Arr::get($menu_item, 'controller', $route_defaults['controller'])))
			{
				continue;
			}

			$menu[$item_name] = array(
				'parent'       => $parent,                                            // parent lavel name
				'title'        => __(Arr::get($menu_item, 'title', '')),                   // anchor title
				'anchor_title' => __(Arr::get($menu_item, 'anchor_title', NULL)),          // anchor long title
				'href'         => $href,                                                   // anchor href
				'class'        => Arr::get($menu_item, 'class', NULL),                     // anchor class name
				'active_class' => NULL,                                                    // active class name
				'directory'    => $route_name,                                             // route directory
				'is_visible'      => Arr::get($menu_item, 'is_visible', TRUE),             // anchor visibility
				'childrens'      => ( ! empty($menu_item['childrens']))                    // submenu
				                ? $this->_gen_menu($menu_item['childrens'], $item_name)
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
	protected function _find_parent(array $menu_array, $active_menu = NULL)
	{
		static $parent;
		static $current_is_query = FALSE;

		if(preg_match('/\?/', $active_menu))
		{
			$current_is_query = TRUE;
		}

		$query = FALSE;

		foreach($menu_array as $name => $item)
		{
			if(preg_match('/(\?|\&)/', $name))
			{
				$query = TRUE;
			}

			$active_menu_name = $active_menu;

			if($query === FALSE AND $current_is_query === TRUE)
			{
				$active_menu_name = strstr($active_menu, '?', TRUE);
			}

			if ($name == $active_menu_name)
			{
				$parent = ($item['parent']) ? $item['parent'] : $name;
			}

			if(! $parent AND ! empty($item['childrens']))
			{
				$parent = $this->_find_parent($item['childrens'], $active_menu_name);
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

		$href = Request::current()->uri();
		$query = NULL;

		foreach($menu as $name => $item)
		{
			if(preg_match('/(?|&)/', $item['href']))
			{
				$query = URL::query();
				exit(Debug::vars($item['href']) . View::factory('profiler/stats'));
			}

			if($item['href'] == $href.$query AND $name != $item['parent'])
			{
				$current = $name;
			}

			if( ! $current AND ! empty($item['childrens']))
			{
				$current = $this->_find_current($item['childrens']);
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

	/**
	 * Recursively clears hidden menu items
	 *
	 * @param array $menu_array
	 * @return array
	 */
	protected function _clear_hidden(array $menu_array)
	{
		$_menu = array();

		foreach($menu_array as $path => $menu)
		{
			if($menu['is_visible'] == TRUE)
			{
				$_menu[$path] = $menu;
			}

			if($menu['childrens'])
			{
				$_menu[$path]['childrens'] = $this->_clear_hidden($menu['childrens']);
			}
		}

		return $_menu;
	}
} // End Menu_Core