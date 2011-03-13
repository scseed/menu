<?php defined('SYSPATH') or die('No direct access allowed.');?>
Roots
<div>
	<ul>
<?php foreach($roots as $root):?>
		<li><?php echo HTML::anchor('menu/tree/' . $root->id, $root->name)?>
			|
			<?php echo HTML::anchor('menu/edit/' . $root->id, '&curren;', array('title' => __('править')))?>
			<?php echo HTML::anchor('menu/delete/' . $root->id, 'x', array('title' => __('удалить')))?>
		</li>
<?php endforeach;?>
	</ul>
</div>
<?php echo HTML::anchor('menu/add/root', __('Создать новое дерево'))?>