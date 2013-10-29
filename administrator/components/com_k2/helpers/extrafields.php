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
 * K2 Extra Fields helper class.
 */

class K2HelperExtraFields extends K2Helper
{

	/**
	 * Holds the available extra fields types.
	 *
	 * @var array $types
	 */
	public static $types = null;

	/**
	 * Holds the available extra fields scopes.
	 *
	 * @var array $types
	 */
	public static $scopes = array(
		'item',
		'category',
		'user',
		'tag'
	);

	/**
	 * Holds the available extra fields groups per scope.
	 *
	 * @var array $groups
	 */
	public static $groups = array();

	/**
	 * Holds the available extra fields definitions.
	 *
	 * @var array $definitions
	 */
	public static $definitions = null;

	public static function getTypes()
	{
		if (is_null(self::$types))
		{
			jimport('joomla.filesystem.folder');
			self::$types = JFolder::folders(JPATH_ADMINISTRATOR.'/components/com_k2/extrafields');
		}
		return self::$types;
	}

	public static function getScopes()
	{
		return self::$scopes;
	}

	public static function getGroups($scope = 'item')
	{
		if (!isset(self::$groups[$scope]))
		{
			K2Model::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_k2/models');
			$model = K2Model::getInstance('ExtraFieldsGroups', 'K2Model');
			$model->setState('scope', $scope);
			self::$groups[$scope] = $model->getRows();
		}
		return self::$groups[$scope];
	}

	public static function getDefinitions()
	{
		if (is_null(self::$definitions))
		{
			jimport('joomla.filesystem.file');
			$definitions = array();
			$types = self::getTypes();
			foreach ($types as $type)
			{
				if (JFile::exists(JPATH_ADMINISTRATOR.'/components/com_k2/extrafields/'.$type.'/definition.php'))
				{
					$field = new JRegistry();
					ob_start();
					include JPATH_ADMINISTRATOR.'/components/com_k2/extrafields/'.$type.'/definition.php';
					$definition = ob_get_contents();
					ob_end_clean();
					$definitions[$type] = $definition;
				}
			}
			self::$definitions = $definitions;
		}
		return self::$definitions;
	}

	public static function getItemExtraFields($categoryId, $values)
	{
		$groups = array();
		$values = json_decode($values);

		foreach (self::getGroups('item') as $group)
		{
			if ($group->assignments->mode == 'all' || ($group->assignments->mode == 'specific' && in_array($categoryId, $group->assignments->categories)))
			{
				foreach ($group->fields as $field)
				{
					if (property_exists($values, $field->id))
					{
						$index = $field->id;
						$resourceValues = $values->$index;
						$defaults = json_decode($field->value);
						$activeValues = array_merge((array)$defaults, (array)$resourceValues);
						$field->value = json_encode((object)$activeValues);
					}
					$field->input = $field->getInput();
				}
				$groups[] = $group;
			}
		}
		return $groups;
	}

}