<?php defined('SYSPATH') or die('No direct access allowed.');?>
<div>
	<?php echo $root->name?>
	<br />
	<?php
	$parent = $root->parent;

	if($parent->loaded())
	{
		$link = Route::url(Route::name(Request::current()->route()), array(
			'controller' => 'menu',
			'action' => 'tree',
			'id' => $parent->id,
		));
	}
	else
	{
		$link = Route::url(Route::name(Request::current()->route()), array(
			'controller' => 'menu',
			'action' => '',
			'id' => '',
		));
	}

	echo HTML::anchor($link, __('move to upper level'));
	?>


	<ul>
<?php foreach($tree as $node):?>
		<li>
			<?php echo ($node->visible) ? '&diams;' : '&loz;'?>
			<?php echo HTML::anchor(
				Route::url(Route::name(Request::current()->route()), array(
					'controller' => 'menu',
					'action' => 'tree',
					'id' => $node->id,
				)),
				$node->title . '(' . $node->name . ')')?>
			|
			<span class="insertions">
				<?php echo HTML::anchor(
					Route::url(Route::name(Request::current()->route()), array(
						'controller' => 'menu',
						'action' => 'add',
						'id' => 'node',
					)) . URL::query(array('root' => $root->id, 'prev' => $node->id)),
					__('+ new node before'))?>
				<?php echo HTML::anchor(
					Route::url(Route::name(Request::current()->route()), array(
						'controller' => 'menu',
						'action' => 'add',
						'id' => 'node',
					)). URL::query(array('root' => $node->id)),
					__('+ child'))?>
				<?php echo HTML::anchor(
					Route::url(Route::name(Request::current()->route()), array(
						'controller' => 'menu',
						'action' => 'add',
						'id' => 'node',
					)). URL::query(array('root' => $root->id, 'next' => $node->id)),
					__('+ new node after'))?>
				|
				<?php echo HTML::anchor(
					Route::url(Route::name(Request::current()->route()), array(
						'controller' => 'menu',
						'action' => 'visibility',
						'id' => $node->id,
					)),
					($node->is_visible) ? __('hide') : __('show'))?>
				|
				<?php echo HTML::anchor(
					Route::url(Route::name(Request::current()->route()), array(
						'controller' => 'menu',
						'action' => 'move',
						'id' => $node->id,
					)) . URL::query(array('direction' => 'up')),
					'&uarr;',
					array('title' => __('move up')))?>
				<?php echo HTML::anchor(
					Route::url(Route::name(Request::current()->route()), array(
						'controller' => 'menu',
						'action' => 'move',
						'id' => $node->id,
					)) . URL::query(array('direction' => 'down')),
					'&darr;',
					array('title' => __('move down')))?>
				<?php echo HTML::anchor(
					Route::url(Route::name(Request::current()->route()), array(
						'controller' => 'menu',
						'action' => 'edit',
						'id' => $node->id,
					)),
					'&curren;',
					array('title' => __('edit')))?>
				<?php echo HTML::anchor(
					Route::url(Route::name(Request::current()->route()), array(
						'controller' => 'menu',
						'action' => 'delete',
						'id' => $node->id,
					)),
					'x',
					array('title' => __('delete')))?>
			</span>
		</li>
<?php endforeach;?>
	</ul>
</div>
<?php echo (count($tree))
	? NULL
	: HTML::anchor(
		Route::url(Route::name(Request::current()->route()), array(
			'controller' => 'menu',
			'action' => 'add',
			'id' => 'node',
		)) . URL::query(array('root' => $root->id)),
		__('create new element on this level'))?>