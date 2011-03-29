<?php defined('SYSPATH') or die('No direct access allowed.');?>
<?php echo Form::open(Request::current())?>
	<legend><?php echo __('Создание корня нового дерева')?></legend>

	<div class="form_item">
	<?php echo Form::label('menu_name', __('Наименование корня дерева'))?>
	<?php echo Form::input('name', $post['name'], array('id' => 'menu_name'))?>
	</div>

	<div class="form_item">
	<?php echo Form::label('menu_route_name', __('Наименование роута по-умолчанию'))?>
	<?php echo Form::input('route_name', $post['route_name'], array('id' => 'menu_route_name'))?>
	</div>

	<div class="form_item">
	<?php echo Form::label('menu_controller', __('Наименование контроллера по-умолчанию'))?>
	<?php echo Form::input('controller', $post['controller'], array('id' => 'menu_controller'))?>
	</div>

	<div class="form_button"><?php echo Form::button(NULL, __('создать'))?> | <?php echo HTML::anchor($back, __('отмена'))?></div>

<?php echo Form::close()?>