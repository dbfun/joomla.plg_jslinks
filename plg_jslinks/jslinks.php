<?php

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

$mainframe->registerEvent('onPrepareContent', 'jslinks');


function jslinks(&$row, &$params, $page=0)
{
	if (is_object($row)) {
		return JShref($row->text, $params);
	}
	return JShref($row, $params);
}

function JShref(&$text, &$params)
{

	/*
	* Собственно все программирование тут
	*/

	// получаем текущую категорию
	$current_category = JRequest::getInt('catid',null, '', 'int'); 

	// получаем параметр "categories" из настроек плагина
	$plugin =& JPluginHelper::getPlugin('content', 'jslinks');
	$pluginParams = new JParameter( $plugin->params ); 
	$categories = $pluginParams->def('categories'); 
	
	// есть категории, где использовать этот плуг?
	if ($categories)
		{
		$categories = explode(',', preg_replace('/[^[:digit:],]/ui','', $categories)); // оставляем только цифры и запятую
		$condition = in_array($current_category, $categories);
		} 
	else $condition = true;
	
	// приступаем к работе если есть {nohref=on} или требуемая категория
	if ($condition OR mb_stripos($text, '{nohref=on}') !== false) {
	
		$text = JString::str_ireplace('{nohref=on}', '', $text); // удаляем внутренний тэг
		$pattern = '/<a (.*?)href(.*?)=(.*?)(\"|\')?(http:\/\/)?(.*?)(\"|\')?(.*?)>(.*?)<\/a>/si'; // ищем ссылку
		preg_match_all($pattern, $text, $out);
		//$text = var_dump($out); return true;
		
		for ($i=0; $i<count($out[0]); $i++)
			{
			if (mb_stripos($out[8][$i], $_SERVER['HTTP_HOST']) === false AND mb_stripos($out[5][$i], 'http') !== false)
				{
				// замену проводим только для внешек
				$repl_quote = $out[0][$i];
				if (mb_stripos($out[8][$i], 'target') === false)
					{
					// добавляем Таргет если он явно не указан
					$repl_quote_pos = mb_stripos($repl_quote, '>');
					$repl_quote = substr($repl_quote,0,$repl_quote_pos).' target="_blank"'.substr($repl_quote,$repl_quote_pos);
					}
				$repl_quote = str_replace("'","\'", $repl_quote);  // заменяем кавычки для JavaScript
				$text = str_replace($out[0][$i],'<noscript>'.$out[9][$i].'</noscript><script type="text/javascript">document.write(\''.$repl_quote.'\');</script>',$text);
				}
			}
			
	}
	return true;
}

?>