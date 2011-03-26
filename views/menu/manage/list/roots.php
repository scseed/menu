<?php defined('SYSPATH') or die('No direct access allowed.');?>
<?php echo __('roots list')?>
<div>
	<ul>
<?php foreach($roots as $root):?>
		<li><?php echo HTML::anchor(
			Request::current()->uri(array(
				'controller' => 'menu',
				'action' => 'tree',
				'id' => $root->id,
			)),
			$root->name
		)?>
			|
			<?php echo HTML::anchor(
				Request::current()->uri(array(
					'controller' => 'menu',
					'action' => 'edit',
					'id' => $root->id,
				)),
				'&curren;',
				array('title' => __('edit')))?>
			<?php echo HTML::anchor(
				Request::current()->uri(array(
					'controller' => 'menu',
					'action' => 'delete',
					'id' => $root->id,
				)),
				'x',
				array('title' => __('delite')))?>
		</li>
<?php endforeach;?>
	</ul>
</div>
<?php echo HTML::anchor(
	Request::current()->uri(array(
		'controller' => 'menu',
		'action' => 'add',
		'id' => 'root',
	)),
	__('create new tree'))?>