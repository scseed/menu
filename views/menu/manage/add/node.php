<?php defined('SYSPATH') or die('No direct access allowed.');?>
<?php echo Form::open('menu/add/root')?>
	<legend>Создание нового элемента ветки <?php echo $root->name?></legend>

	<div class="form_item">
	<?php echo Form::label('menu_name', 'Наименование корня дерева')?>
	<?php echo Form::input('name', $post['name'], array('id' => 'menu_name'))?>
	</div>

	<div class="form_item">
	<?php echo Form::label('menu_route_name', 'Наименование роута по-умолчанию')?>
	<?php echo Form::input('route_name', $post['route_name'], array('id' => 'menu_route_name'))?>
	</div>

	<div class="form_item">
	<?php echo Form::label('menu_controller', 'Наименование контроллера по-умолчанию')?>
	<?php echo Form::input('controller', $post['controller'], array('id' => 'menu_controller'))?>
	</div>

	<div class="form_button"><?php echo Form::button(NULL, 'создать')?></div>

<?php echo Form::close()?>