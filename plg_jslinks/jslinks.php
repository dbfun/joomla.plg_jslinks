<?php

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

$mainframe->registerEvent('onPrepareContent', 'jslinks');


function jslinks(&$row, &$params, $page=0) {
  if (is_object($row)) {
    return JShref($row->text, $params);
  }
  return JShref($row, $params);
}

function JSReplaceExtend($m) {
  $add = preg_replace('~target\s*=\s*(\"|\').*?(\"|\')~i', '', "$m[1] $m[5]") . ' target="_blank"';
  $ret = "<noindex><a {$add} href=\"{$m[3]}\" rel=\"nofollow\">{$m[6]}</a></noindex>";
  return $ret;
}

function JShref(&$text, &$params) {

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
  if ($categories) {
    $categories = explode(',', preg_replace('/[^[:digit:],]/ui','', $categories)); // оставляем только цифры и запятую
    $condition = in_array($current_category, $categories);
  } else {
    $condition = true;
  }

  // приступаем к работе если есть {nohref=on} или требуемая категория
  if ($condition || mb_stripos($text, '{nohref=on}') !== false) {
    $text = JString::str_ireplace('{nohref=on}', '', $text); // удаляем внутренний тэг
    $text = preg_replace_callback('~<a\s+(.*?)href\s*=\s*(\"|\')(https?:\/\/.*?)(\"|\')(.*?)>(.*?)<\/a>~si', 'JSReplaceExtend', $text);
  }
  return true;
}