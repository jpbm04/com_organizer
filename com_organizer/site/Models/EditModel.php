<?php
/**
 * @package     Organizer
 * @extension   com_organizer
 * @author      James Antrim, <james.antrim@nm.thm.de>
 * @copyright   2020 TH Mittelhessen
 * @license     GNU GPL v.3
 * @link        www.thm.de
 */

namespace Organizer\Models;

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\MVC\Model\AdminModel;
use Organizer\Helpers\Can;
use Organizer\Helpers\Input;
use Organizer\Helpers\Languages;
use Organizer\Helpers\Named;

/**
 * Class loads item form data to edit an entry.
 */
abstract class EditModel extends AdminModel
{
	use Named;

	protected $deptResource;

	public $item = null;

	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 */
	public function __construct($config = [])
	{
		parent::__construct($config);

		$this->setContext();
	}

	/**
	 * Provides a strict access check which can be overwritten by extending classes.
	 *
	 * @return bool  true if the user can access the view, otherwise false
	 */
	protected function allowEdit()
	{
		return Can::administrate();
	}

	/**
	 * Method to get the form
	 *
	 * @param   array  $data      Data         (default: array)
	 * @param   bool   $loadData  Load data  (default: true)
	 *
	 * @return mixed Form object on success, False on error.
	 * @throws Exception => unauthorized access
	 *
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	public function getForm($data = [], $loadData = true)
	{
		$name = $this->get('name');
		$form = $this->loadForm($this->context, $name, ['control' => 'jform', 'load_data' => $loadData]);

		return empty($form) ? false : $form;
	}

	/**
	 * Method to get a single record.
	 *
	 * @param   integer  $pk  The id of the primary key.
	 *
	 * @return mixed    Object on success, false on failure.
	 * @throws Exception => unauthorized access
	 */
	public function getItem($pk = null)
	{
		if (!Factory::getUser()->id)
		{
			throw new Exception(Languages::_('ORGANIZER_401'), 401);
		}

		$pk = empty($pk) ? Input::getSelectedID() : $pk;

		// Prevents duplicate execution from getForm and getItem
		if (isset($this->item->id) and ($this->item->id === $pk or $pk === null))
		{
			return $this->item;
		}

		$this->item = parent::getItem($pk);

		if (!$this->allowEdit())
		{
			throw new Exception(Languages::_('ORGANIZER_403'), 401);
		}

		return $this->item;
	}

	/**
	 * Method to get a form object.
	 *
	 * @param   string   $name     The name of the form.
	 * @param   string   $source   The form source. Can be XML string if file flag is set to false.
	 * @param   array    $options  Optional array of options for the form creation.
	 * @param   boolean  $clear    Optional argument to force load a new form.
	 * @param   string   $xpath    An optional xpath to search for the fields.
	 *
	 * @return  Form|boolean  \JForm object on success, false on error.
	 */
	protected function loadForm($name, $source = null, $options = array(), $clear = false, $xpath = '')
	{
		Form::addFormPath(JPATH_COMPONENT_SITE . '/Forms');
		Form::addFieldPath(JPATH_COMPONENT_SITE . '/Fields');

		return parent::loadForm($name, $source, $options, $clear, $xpath);
	}

	/**
	 * Method to load the form data.
	 *
	 * @return object
	 * @throws Exception => unauthorized access
	 */
	protected function loadFormData()
	{
		$resourceIDs = Input::getSelectedIDs();
		$resourceID  = empty($resourceIDs) ? 0 : $resourceIDs[0];

		return $this->item ? $this->item : $this->getItem($resourceID);
	}
}
