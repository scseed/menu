<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * Template Controller Core_Menu
 *
 * @package Menu
 * @author  Sergei Gladkovskiy <smgladkovskiy@gmail.com>
 */
abstract class Controller_Core_Menu extends Controller_Template {

	/**
	 * @return void
	 */
	public function action_index()
	{
		$roots = Jelly::query('menu')->where('lvl', '=', 0)->select();
		$this->template->content = View::factory('menu/manage/list/roots')->bind('roots', $roots);
	}

	/**
	 * @throws Http_Exception_404
	 * @return void
	 */
	public function action_tree()
	{
		$root_id = (int) $this->request->param('id');

		if( ! $root_id)
			throw new Http_Exception_404('Root id is not specified');

		$root = Jelly::query('menu', $root_id)->select();

		if( ! $root->loaded())
			throw new Http_Exception_404('Root node with id = :id was not found', array(':id' => $root_id));

		$tree = $root->descendants(FALSE, 'ASC', TRUE);

		$this->template->content = View::factory('menu/manage/tree')
			->bind('root', $root)
			->bind('tree', $tree);
	}

	/**
	 * @return void
	 */
	public function action_add()
	{
		$element = $this->request->param('id');
		$root    = Arr::get($_GET, 'root', NULL);

		$method = '_add_' . $element;

		$this->template->content = (method_exists(__CLASS__, $method))
			? $this->{$method}($root)
			: $this->request->redirect(Request::current()->uri(array('controller' => 'menu', 'action' => '')));
	}

	/**
	 * @throws Http_Exception_404|Validation_Exception
	 * @return void
	 */
	public function action_edit()
	{
		$node_id = $this->request->param('id');

		if( ! $node_id)
			throw new Http_Exception_404('Node id is not specified');

		$node = Jelly::query('menu', $node_id)->select();

		$back  = $this->request->referrer();

		$model_params = ($node->params) ? $node->params : NULL;

		if($model_params !== NULL)
		{
			foreach($node->params as $name => $value)
			{
				$params[] = $name . '=' . $value;
			}
		}

		$_post = array(
			'name' => $node->name,
			'title' => $node->title,
			'route_name' => $node->route_name,
			'directory' => $node->directory,
			'controller' => $node->controller,
			'action' => $node->action,
			'params' => ($model_params) ? implode(';', $params) : NULL,
			'query' => $node->query,
			'visible' => $node->visible,
		);

		$visibilities = array(
			1 => __('виден'),
			0 => __('не виден'),
		);
		if($_POST)
		{
			$post = Arr::extract($_POST, array_keys($_post));

			$_params = ($post['params']) ? explode(';', $post['params']) : array();

			$params = array();
			foreach($_params as $param_string)
			{
				$params += array(strstr($param_string, '=', TRUE) => str_replace('=', '', strstr($param_string, '=')));
			}

			$post['params'] = $params;

			$node->set($post);

			try
			{
				$node->save();

				$link = ($node->parent()->id)
					? Request::current()->uri(array('controller' => 'menu','action' => 'tree','id' => $node->parent()->id))
					: Request::current()->uri(array('controller' => 'menu','action' => '','id' => NULL));
				$this->request->redirect($link);
			}
			catch(Validation_Exception $e)
			{
				throw $e;
			}
		}

		if(isset($post))
		{
			$post = Arr::merge($_post, $post);
		}
		else
		{
			$post = $_post;
		}

		$this->template->content = View::factory('menu/manage/edit')
			->bind('visibilities', $visibilities)
			->bind('node', $node)
			->bind('back', $back)
			->bind('post', $post);
	}

	/**
	 * @throws Http_Exception_404
	 * @return void
	 */
	public function action_move()
	{
		$id        = (int) $this->request->param('id');
		$direction = Arr::get($_GET, 'direction', NULL);

		if( ! $id)
			throw new Http_Exception_404('Node id is not specified');

		$node = Jelly::query('menu', $id)->select();

		if( ! $node->loaded())
			throw new Http_Exception_404('Menu node with id = :id was not found', array(':id' => $id));

		switch($direction)
		{
			case 'up':
				$sibling = Jelly::query('menu')
					->where('scope', '=', $node->scope)
					->where('level', '=', $node->level)
					->where('right', '=', $node->left - 1)
					->limit(1)
					->select();

				if($sibling->loaded())
					$sibling->move_to_next_sibling($node);

				break;
			case 'down':
				$sibling = Jelly::query('menu')
					->where('scope', '=', $node->scope)
					->where('level', '=', $node->level)
					->where('left', '=', $node->right + 1)
					->limit(1)
					->select();

				if($sibling->loaded())
					$node->move_to_next_sibling($sibling);
				break;
			default:
				break;
		}

		$this->request->redirect($this->request->referrer());
	}

	/**
	 * @throws Http_Exception_404
	 * @return void
	 */
	public function action_delete()
	{
		$id = (int) $this->request->param('id');

		if( ! $id)
			throw new Http_Exception_404('Node id is not specified');

		$node = Jelly::query('menu', $id)->select()->delete_obj();

		$this->request->redirect($this->request->referrer());
	}

	/**
	 * @throws Http_Exception_404|Validation_Exception
	 * @return void
	 */
	public function action_visibility()
	{
		$id = (int) $this->request->param('id');

		if( ! $id)
			throw new Http_Exception_404('Node id is not specified');

		$node = Jelly::query('menu', $id)->select();

		if( ! $node->loaded())
			throw new Http_Exception_404('Menu node with id = :id was not found', array(':id' => $id));

		$node->visible = ! $node->visible;

		try
		{
			$node->save();
		}
		catch(Validation_Exception $e)
		{
			throw new Kohana_Exception($e);
		}

		$this->request->redirect($this->request->referrer());
	}

	/**
	 * @throws Validation_Exception
	 * @return Kohana_View
	 */
	protected function _add_root()
	{
		$back  = $this->request->referrer();
		$_post = array(
			'name'       => NULL,
			'route_name' => NULL,
			'controller' => NULL,
		);

		if($_POST)
		{
			$post = Arr::extract($_POST, array_keys($_post));

			$last_scope_menu = Jelly::query('menu')->distinct('scope')->order_by('scope', 'DESC')->limit(1)->select();
			$scope           = ($last_scope_menu->loaded()) ? $last_scope_menu->scope : 0;
			$post['visible'] = 0;
			$new_root        = Jelly::factory('menu');

			$new_root->set($post);

			try
			{
				$new_root->save();

				$new_root->insert_as_new_root($scope + 1);

				$this->request->redirect(Request::current()->uri(array('controller' => 'menu', 'action' => '')));
			}
			catch(Validation_Exception $e)
			{
				throw new Kohana_Exception($e);
			}
		}

		if(!isset($post))
		{
			$post = $_post;
		}
		else
		{
			$post = Arr::merge($_post, $post);
		}

		return View::factory('menu/manage/add/root')
			->bind('post', $post)
			->bind('back', $back);
	}

	/**
	 * @throws Validation_Exception
	 * @return Kohana_View
	 */
	protected function _add_node()
	{
		$back    = $this->request->referrer();
		$root_id = Arr::get($_GET, 'root', NULL);
		$prev_id = Arr::get($_GET, 'prev', NULL);
		$next_id = Arr::get($_GET, 'next', NULL);
		$root    = ($root_id) ? Jelly::query('menu', (int) $root_id)->select() : NULL;
		$prev    = ($prev_id) ? Jelly::query('menu', (int) $prev_id)->select() : NULL;
		$next    = ($next_id) ? Jelly::query('menu', (int) $next_id)->select() : NULL;

		$_post = array(
			'name' => NULL,
			'title' => NULL,
			'route_name' => ($root) ? $root->route_name : NULL,
			'directory' => NULL,
			'controller' => NULL,
			'action' => NULL,
			'params' => NULL,
			'query' => NULL,
			'visible' => TRUE,
		);
		$visibilities = array(
			1 => __('виден'),
			0 => __('не виден'),
		);

		if($_POST)
		{
			$post     = Arr::extract($_POST, array_keys($_post));
			$new_node = Jelly::factory('menu');

			$_params = ($post['params']) ? explode(';', $post['params']) : array();

			$params = array();
			foreach($_params as $param_string)
			{
				$params += array(strstr($param_string, '=', TRUE) => str_replace('=', '', strstr($param_string, '=')));
			}

			$post['params'] = $params;

			$new_node->set($post);

			try
			{
				if($prev)
				{
					$new_node->insert_as_prev_sibling($prev);
				}
				elseif($next)
				{
					$new_node->insert_as_next_sibling($next);
				}
				else
				{
					$new_node->insert_as_first_child($root);
				}

				$this->request->redirect(
					Request::current()->uri(array(
						'controller' => 'menu',
						'action' => 'tree',
						'id' => $root->id
					))
				);
			}
			catch(Validation_Exception $e)
			{
				throw new Kohana_Exception($e);
			}
		}

		if(!isset($post))
		{
			$post = $_post;
		}
		else
		{
			$post = Arr::merge($_post, $post);
		}

		return View::factory('menu/manage/add/node')
			->bind('root', $root)
			->bind('visibilities', $visibilities)
			->bind('post', $post)
			->bind('back', $back);
	}

} // End Controller_Core_Menu