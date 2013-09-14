<?php defined('SYSPATH') or die('No direct access allowed.');?>
<h3><?php echo $root->name?></h3>
<p>
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

	echo HTML::anchor($link, '<i class="icon-reply"></i> '.__('на уровень вверх'), array('class' => 'btn btn-mini btn-inverse'));
	?>
</p>
<table class="table table-striped table-bordered table-hover">
	<thead>
	<tr>
		<th>Позиция</th>
		<th>Наименование</th>
		<th>Раздел</th>
		<th>Контроллер</th>
		<th>Экшн</th>
		<th>Статус</th>
		<th></th>
	</tr>
	</thead>
<?php $i=0; $count = count($tree); foreach($tree as $node): $i++;?>
		<tr>
			<td>
				<?php echo ($i !== 1) ? HTML::anchor(
					Route::url(Route::name(Request::current()->route()), array(
						'controller' => 'menu',
						'action' => 'move',
						'id' => $node->id,
					)) . URL::query(array('direction' => 'up')),
					'<i class="icon-arrow-up"></i>',
					array('rel' => 'tooltip', 'title' => __('move up'))
				).'&nbsp;' : NULL?>
				<?php echo ($i !== $count) ? HTML::anchor(
					Route::url(Route::name(Request::current()->route()), array(
						'controller' => 'menu',
						'action' => 'move',
						'id' => $node->id,
					)) . URL::query(array('direction' => 'down')),
					'<i class="icon-arrow-down"></i>',
					array('rel' => 'tooltip', 'title' => __('move down'))
				) : NULL?>
			</td>
			<td><?php echo HTML::anchor(
					Route::url(Route::name(Request::current()->route()), array(
						'controller' => 'menu',
						'action' => 'tree',
						'id' => $node->id,
					)),
				$node->title)?></td>
			<td><?php echo $node->directory?></td>
			<td><?php echo $node->controller?></td>
			<td><?php echo $node->action?></td>
			<td><?php echo ($node->is_visible) ? '<i class="icon-eye-open"></i>' : '<i class="icon-eye-close"></i>'?></td>
			<td>
				<div class="btn-group">
					<a class="btn btn-small dropdown-toggle" data-toggle="dropdown" href="#">
						<i class="icon-cog"></i>
						<span class="caret"></span>
					</a>
					<ul class="dropdown-menu pull-right">
						<li>
							<?php echo HTML::anchor(
								Route::url(Route::name(Request::current()->route()), array(
									'controller' => 'menu',
									'action' => 'add',
									'id' => 'node',
								)) . URL::query(array('root' => $root->id, 'prev' => $node->id)),
								'<i class="icon-plus"></i> '.__('new node before'))?>
						</li>
						<li>
							<?php echo HTML::anchor(
								Route::url(Route::name(Request::current()->route()), array(
									'controller' => 'menu',
									'action' => 'add',
									'id' => 'node',
								)). URL::query(array('root' => $node->id)),
								'<i class="icon-plus"></i> '.__('child'))?>
						</li>
						<li>
							<?php echo HTML::anchor(
								Route::url(Route::name(Request::current()->route()), array(
									'controller' => 'menu',
									'action' => 'add',
									'id' => 'node',
								)). URL::query(array('root' => $root->id, 'next' => $node->id)),
								'<i class="icon-plus"></i> '.__('new node after'))?>
						</li>
						<li class="divider"></li>
						<li>
							<?php echo HTML::anchor(
								Route::url(Route::name(Request::current()->route()), array(
									'controller' => 'menu',
									'action' => 'visibility',
									'id' => $node->id,
								)),
								($node->is_visible) ? '<i class="icon-eye-close"></i> '.__('hide') : '<i class="icon-eye-open"></i> '.__('show'))?>
						</li>
						<li>
							<?php echo HTML::anchor(
								Route::url(Route::name(Request::current()->route()), array(
									'controller' => 'menu',
									'action' => 'edit',
									'id' => $node->id,
								)),
								'<i class="icon-edit"></i> '.__('edit')
								)?>
						</li>
						<li>
							<?php echo HTML::anchor(
								Route::url(Route::name(Request::current()->route()), array(
									'controller' => 'menu',
									'action' => 'delete',
									'id' => $node->id,
								)),
								'<i class="icon-trash"></i> '.__('delete')
								)?>
						</li>
					</ul>
				</div>
			</td>
		</tr>
<?php endforeach;?>
</table>
<p><?php echo HTML::anchor($link, '<i class="icon-reply"></i> '.__('на уровень вверх'), array('class' => 'btn btn-mini btn-inverse'));?></p>
<?php echo (count($tree))
	? NULL
	: HTML::anchor(
		Route::url(Route::name(Request::current()->route()), array(
			'controller' => 'menu',
			'action' => 'add',
			'id' => 'node',
		)) . URL::query(array('root' => $root->id)),
		__('create new element on this level'))?>