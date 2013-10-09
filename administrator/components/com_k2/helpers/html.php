<?php
/**
 * @version		3.0.0
 * @package		K2
 * @author		JoomlaWorks http://www.joomlaworks.net
 * @copyright	Copyright (c) 2006 - 2013 JoomlaWorks Ltd. All rights reserved.
 * @license		GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */

// no direct access
defined('_JEXEC') or die ;

require_once JPATH_ADMINISTRATOR.'/components/com_k2/helpers/helper.php';

/**
 * K2 HTML helper class.
 */

class K2HelperHTML extends K2Helper
{

	public static function published($name = 'published', $value = null)
	{
		$options = array();
		$options[] = JHTML::_('select.option', '', JText::_('K2_ALL'));
		$options[] = JHTML::_('select.option', 1, JText::_('K2_YES'));
		$options[] = JHTML::_('select.option', 0, JText::_('K2_NO'));
		return JHtml::_('select.radiolist', $options, $name, '', 'value', 'text', $value);
	}

	public static function featured($name = 'featured', $value = null)
	{
		$options = array();
		$options[] = JHTML::_('select.option', '', JText::_('K2_ALL'));
		$options[] = JHTML::_('select.option', 1, JText::_('K2_YES'));
		$options[] = JHTML::_('select.option', 0, JText::_('K2_NO'));
		return JHtml::_('select.radiolist', $options, $name, '', 'value', 'text', $value);
	}

	public static function language($name = 'language', $none = true, $value = null)
	{
		$options = JHtml::_('contentlanguage.existing', true, true);
		if($none)
		{
			array_unshift($options, JHtml::_('select.option', '', JText::_('K2_ANY')));
		}
		return JHtml::_('select.genericlist', $options, $name, '', 'value', 'text', $value);
	}

	public static function sorting($options = array(), $name = 'sorting', $value = null)
	{
		$list = array();
		foreach ($options as $optionLabel => $optionValue)
		{
			$list[] = JHTML::_('select.option', $optionValue, JText::_($optionLabel));
		}

		return JHtml::_('select.genericlist', $list, $name, '', 'value', 'text', $value);
	}

	public static function categories($name = 'catid', $none = false, $value = null)
	{
		$model = K2Model::getInstance('Categories', 'K2Model');
		$model->setState('sorting', 'ordering');
		$rows = $model->getRows();
		$options = array();
		if ($none)
		{
			$options[] = JHtml::_('select.option', '', JText::_('K2_ANY'));
		}
		foreach ($rows as $row)
		{
			$title = str_repeat('-', intval($row->level) - 1).$row->title;
			if ($row->trashed)
			{
				$title .= JText::_('K2_TRASHED_CATEGORY_NOTICE');
			}
			else if (!$row->published)
			{
				$title .= JText::_('K2_UNPUBLISHED_CATEGORY_NOTICE');
			}
			$options[] = JHtml::_('select.option', $row->id, $title);
		}
		return JHtml::_('select.genericlist', $options, $name, '', 'value', 'text', $value);
	}
	
	public static function search($name = 'search')
	{
		return '<input type="text" class="appActionSearch"  name="' . $name . '" />';
	}

}
