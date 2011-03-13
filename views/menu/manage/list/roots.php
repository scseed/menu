<?php defined('SYSPATH') or die('No direct access allowed.');?>
Roots
<div>
	<ul>
<?php foreach($roots as $root):?>
		<li><?php echo HTML::anchor('menu/tree/' . $root->id, $root->name)?></li>
<?php endforeach;?>
	</ul>
</div>
<?php echo HTML::anchor('menu/add/root', __('Создать новое дерево'))?>