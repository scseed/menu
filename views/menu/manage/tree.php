<?php defined('SYSPATH') or die('No direct access allowed.');?>
<div>
	<?php echo $root->name?>
	<ul>
<?php foreach($tree as $node):?>
		<li>
			<?php echo $node->name?>
			<span class="insertions">
				<?php echo HTML::anchor('menu/add/node' . URL::query(array('root' => $root->id, 'prev' => $node->id)), 'вставить до')?>
				<?php echo HTML::anchor('menu/add/node' . URL::query(array('root' => $node->id)), 'добавить дочерний элемент')?>
				<?php echo HTML::anchor('menu/add/node' . URL::query(array('root' => $root->id, 'next' => $node->id)), 'вставить после')?>
			</span>
		</li>
<?php endforeach;?>
	</ul>
</div>
<?php echo HTML::anchor('menu/add/node' . URL::query(array('root' => $root->id)), 'создать новый дочерний элемент')?>