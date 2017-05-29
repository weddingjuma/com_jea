<?php
/**
 * This file is part of Joomla Estate Agency - Joomla! extension for real estate agency
 *
 * @package     Joomla.Administrator
 * @subpackage  com_jea
 * @copyright   Copyright (C) 2008 - 2017 PHILIP Sylvain. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.filesystem.file');

/**
 * Gateway model class.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_jea
 *
 * @see         JModelAdmin
 *
 * @since       3.4
 */
class JeaModelGateway extends JModelAdmin
{
	/**
	 * Overrides parent method
	 *
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  JForm|boolean  A JForm object on success, false on failure
	 *
	 * @see JModelForm::getForm()
	 */
	public function getForm($data = array(), $loadData = true)
	{
		$app = JFactory::getApplication();
		$type = $app->getUserStateFromRequest('com_jea.gateway.type', 'type', '', 'cmd');

		/* @var $form JForm */
		$form = $this->loadForm('com_jea.' . $type, $type, array('control' => 'jform', 'load_data' => false));

		if (empty($form))
		{
			return false;
		}

		$item = $this->getItem();

		// Load gateway params
		if ($item->id)
		{
			$formConfigFile = JPATH_COMPONENT_ADMINISTRATOR . '/gateways/providers/' . $item->provider . '/' . $item->type . '.xml';

			if (JFile::exists($formConfigFile))
			{
				$gatewayForm = $this->loadForm('com_jea.' . $item->type . '.' . $item->provider, $formConfigFile, array('load_data' => false));
				$form->load($gatewayForm->getXml());
			}

			$data = $this->loadFormData();
			$form->bind($data);
		}

		return $form;
	}

	/**
	 * Overrides parent method
	 *
	 * @return  array  The default data is an empty array.
	 *
	 * @see JModelForm::loadFormData()
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data. See JControllerForm::save()
		$data = JFactory::getApplication()->getUserState('com_jea.edit.gateway.data', array());

		if (empty($data))
		{
			$data = $this->getItem();
		}

		return $data;
	}

	/**
	 * Overrides parent method
	 *
	 * @param   array  $data  The form data.
	 *
	 * @return  boolean  True on success, False on error.
	 *
	 * @see JModelAdmin::save()
	 */
	public function save($data)
	{
		if (isset($data['params']) && is_array($data['params']))
		{
			$data['params'] = json_encode($data['params']);
		}

		return parent::save($data);
	}
}
