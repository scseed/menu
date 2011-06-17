<?php defined('SYSPATH') or die('No direct access allowed.');?>
<div id="langs">
	<ul>
	<?php
foreach($languages as $lang)
{
	if($lang->abbr == $current_lang)
	{
		$link = FALSE;
	}
	else
	{
		$link = TRUE;
	}

	$current_controller = (Request::current()->controller() == 'home')  ? '' : Request::current()->controller();
	$current_action     = (Request::current()->action()     == 'index') ? '' : Request::current()->action();

	echo '<li>';
	echo ($link === TRUE)
		? HTML::anchor(
			Request::current()->uri(array(
				'lang' => $lang->abbr,
				'controller' => $current_controller,
				'action' => $current_action
			)).URL::query(),
			HTML::image($icons[$lang->abbr], array('alt' => __($lang->locale_name), 'class' => 'ico png')),
			array('title' => __($lang->locale_name))).' '
		: HTML::image(
			$icons[$lang->abbr],
			array(
				'alt'   => __($lang->locale_name),
				'title' => __($lang->locale_name),
				'class' => 'ico png'
			)).' ';
	echo '</li>';
}?>
	</ul>
</div>