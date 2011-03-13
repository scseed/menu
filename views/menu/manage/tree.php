<?php defined('SYSPATH') or die('No direct access allowed.');?>
<div>
	<?php echo $root->name?>
	<br />
	<?php
	 $parent = $root->parent;

	if($parent->loaded())
	{
		$link = 'menu/tree/' . $root->parent()->id;
	}
	else
	{
		$link = 'menu';
	}

	echo HTML::anchor($link, __('вернуться на уровень выше'));
	?>


	<ul>
<?php foreach($tree as $node):?>
		<li>
			<?php echo ($node->visible) ? '&diams;' : '&loz;'?> <?php echo HTML::anchor('menu/tree/' . $node->id, $node->name)?>
			|
			<span class="insertions">
				<?php echo HTML::anchor('menu/add/node' . URL::query(array('root' => $root->id, 'prev' => $node->id)), __('вставить до'))?>
				<?php echo HTML::anchor('menu/add/node' . URL::query(array('root' => $node->id)), __('добавить дочерний элемент'))?>
				<?php echo HTML::anchor('menu/add/node' . URL::query(array('root' => $root->id, 'next' => $node->id)), __('вставить после'))?>
				|
				<?php echo HTML::anchor('menu/visibility/' . $node->id, ($node->visible) ? __('скрыть') : __('отобразить'))?>
				|
				<?php echo HTML::anchor('menu/move/' . $node->id . URL::query(array('direction' => 'up')), '&uarr;', array('title' => __('переместить вверх')))?>
				<?php echo HTML::anchor('menu/move/' . $node->id . URL::query(array('direction' => 'down')), '&darr;', array('title' => __('переместить вниз')))?>
				<?php echo HTML::anchor('menu/delete/' . $node->id, 'x', array('title' => __('удалить')))?>
			</span>
		</li>
<?php endforeach;?>
	</ul>
</div>
<?php echo HTML::anchor('menu/add/node' . URL::query(array('root' => $root->id)), __('создать новый элемент на этом уровне'))?>