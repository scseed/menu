<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * Template Controller menu
 *
 * @author Sergei Gladkovskiy <smgladkovskiy@gmail.com>
 * @copyrignt
 */
abstract class Controller_Core_Menu extends Controller_Template {

	public function action_index()
	{
		$roots = Jelly::query('menu')->where('lvl', '=', 0)->select();
		$this->template->content = View::factory('menu/manage/list/roots')->bind('roots', $roots);
	}

	public function action_tree()
	{
		$root_id = (int) $this->request->param('id');

		$root = Jelly::query('menu', $root_id)->select();

		$tree = $root->descendants();

		$this->template->content = View::factory('menu/manage/tree')
			->bind('root', $root)
			->bind('tree', $tree);
	}

	public function action_add()
	{
		$element = $this->request->param('id');
		$root    = Arr::get($_GET, 'root', NULL);

		$method = '_add_' . $element;

		$this->template->content = (method_exists(__CLASS__, $method))
			? $this->{$method}($root)
			: $this->request->redirect('menu');
	}

	protected function _add_root()
	{
		$post = array(
			'name' => NULL,
			'route_name' => NULL,
			'controller' => NULL,
		);

		if($_POST)
		{
			$post = Arr::extract($_POST, array_keys($post));

			$last_scope_menu = Jelly::query('menu')->distinct('scope')->order_by('scope', 'DESC')->limit(1)->select();
			$scope           = ($last_scope_menu->loaded()) ? $last_scope_menu->scope : 0;
			$post['visible'] = 0;
			$new_root        = Jelly::factory('menu');

			$new_root->set($post);

			try
			{
				$new_root->save();

				$new_root->insert_as_new_root($scope + 1);

				$this->request->redirect('menu');
			}
			catch(Validation_Exception $e)
			{
				$errors = $e->array->errors('common_validation');
			}
		}

		return View::factory('menu/manage/add/root')
			->bind('post', $post);
	}

	protected function _add_node()
	{
		return View::factory('menu/manage/add/node');
	}

} // End Controller_Core_Menu