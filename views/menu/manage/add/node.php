<?php defined('SYSPATH') or die('No direct access allowed.');?>
<?php echo Form::open('menu/add/node'.URL::query())?>
	<legend><?php echo __('Создание нового элемента раздела')?> "<?php echo $root->name?>"</legend>

	<div class="form_item">
	<?php echo Form::label('menu_name', __('Наименование пункта меню'))?>
	<?php echo Form::input('name', $post['name'], array('id' => 'menu_name'))?>
	</div>

	<div class="form_item">
	<?php echo Form::label('menu_route_name', __('Наименование роута'))?>
	<?php echo Form::input('route_name', $post['route_name'], array('id' => 'menu_route_name'))?>
	</div>
	<div class="form_item">
	<?php echo Form::label('menu_controller', __('Наименование контроллера'))?>
	<?php echo Form::input('controller', $post['controller'], array('id' => 'menu_controller'))?>
	</div>
	<div class="form_item">
	<?php echo Form::label('menu_controller', __('Наименование экшена'))?>
	<?php echo Form::input('action', $post['action'], array('id' => 'menu_action'))?>
	</div>
	<div class="form_item">
	<?php echo Form::label('menu_controller', __('Статус видимости'))?>
	<?php echo Form::select('visible', $visibilities, $post['visible'], array('id' => 'menu_visible'))?>
	</div>

	<div class="form_button"><?php echo Form::button(NULL, __('создать'))?> | <?php echo HTML::anchor($back, __('отмена'))?></div>

<?php echo Form::close()?>