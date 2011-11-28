<?php defined('SYSPATH') or die('No direct access allowed.');?>
<?php echo Form::open(Request::current())?>
	<legend><?php echo __('Правка элемента')?> "<?php echo $node->name?>"</legend>

	<div class="form_item">
	<?php echo Form::label('menu_name', __('Наименование пункта меню'))?>
	<?php echo Form::input('name', $post['name'], array('id' => 'menu_name'))?>
	</div>
	<div class="form_item">
	<?php echo Form::label('menu_title', __('Заголовок пункта меню'))?>
	<?php echo Form::input('title', $post['title'], array('id' => 'menu_title'))?>
	</div>
	<div class="form_item">
	<?php echo Form::label('menu_anchor_title', __('Расширенный заголовок пункта меню'))?>
	<?php echo Form::input('anchor_title', $post['anchor_title'], array('id' => 'menu_anchor_title'))?>
	</div>
	<div class="form_item">
	<?php echo Form::label('menu_route_name', __('Наименование роута'))?>
	<?php echo Form::input('route_name', $post['route_name'], array('id' => 'menu_route_name'))?>
	</div>
	<div class="form_item">
	<?php echo Form::label('menu_directory', __('Наименование директории'))?>
	<?php echo Form::input('directory', $post['directory'], array('id' => 'menu_directory'))?>
	</div>
	<div class="form_item">
	<?php echo Form::label('menu_controller', __('Наименование контроллера'))?>
	<?php echo Form::input('controller', $post['controller'], array('id' => 'menu_controller'))?>
	</div>
	<div class="form_item">
	<?php echo Form::label('menu_action', __('Наименование экшена'))?>
	<?php echo Form::input('action', $post['action'], array('id' => 'menu_action'))?>
	</div>
	<div class="form_item">
	<?php echo Form::label('menu_params', __('Параметры запроса'))?>
	<?php echo Form::input('params', $post['params'], array('id' => 'menu_params'))?>
	</div>
	<div class="form_item">
	<?php echo Form::label('menu_query', __('GET параметры'))?>
	<?php echo Form::input('query', $post['query'], array('id' => 'menu_query'))?>
	</div>
	<div class="form_item">
	<?php echo Form::label('menu_class', __('Class пункта меню'))?>
	<?php echo Form::input('class', $post['class'], array('id' => 'menu_class'))?>
	</div>
	<div class="form_item">
	<?php echo Form::label('menu_is_visible', __('Статус видимости'))?>
	<?php echo Form::select('is_visible', $visibilities, $post['is_visible'], array('id' => 'menu_is_visible'))?>
	</div>

	<div class="form_button"><?php echo Form::button(NULL, __('создать'))?> | <?php echo HTML::anchor($back, __('отмена'))?></div>

<?php echo Form::close()?>