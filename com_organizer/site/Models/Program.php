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
use Joomla\CMS\Table\Table;
use Organizer\Helpers\Can;
use Organizer\Helpers\Input;
use Organizer\Tables\Programs as ProgramsTable;

/**
 * Class which manages stored (degree) program data.
 */
class Program extends BaseModel
{
	/**
	 * Attempts to delete the selected degree program entries and related mappings
	 *
	 * @return boolean  True if successful, false if an error occurs.
	 * @throws Exception => unauthorized access
	 */
	public function delete()
	{
		if (!Can::documentTheseDepartments())
		{
			throw new Exception(Languages::_('ORGANIZER_403'), 403);
		}

		if ($programIDs = Input::getSelectedIDs())
		{
			$table = new ProgramsTable;
			$model = new Mapping;
			foreach ($programIDs as $programID)
			{
				if (!Can::document('program', $programID))
				{
					throw new Exception(Languages::_('ORGANIZER_403'), 403);
				}

				if (!$model->deleteByResourceID($programID, 'program'))
				{
					return false;
				}

				if (!$table->delete($programID))
				{
					return false;
				}
			}
		}

		return true;
	}

	/**
	 * Method to get a table object, load it if necessary.
	 *
	 * @param   string  $name     The table name. Optional.
	 * @param   string  $prefix   The class prefix. Optional.
	 * @param   array   $options  Configuration array for model. Optional.
	 *
	 * @return Table A Table object
	 *
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	public function getTable($name = '', $prefix = '', $options = [])
	{
		return new ProgramsTable;
	}

	/**
	 * Method to save degree programs
	 *
	 * @param   array  $data  the data to be used to create the program when called from the program helper
	 *
	 * @return Boolean
	 * @throws Exception => invalid request / unauthorized access
	 */
	public function save($data = [])
	{
		$data = empty($data) ? Input::getFormItems()->toArray() : $data;

		if (empty($data['id']))
		{
			$documentationAccess = (bool) Can::documentTheseDepartments();
			$schedulingAccess    = (bool) Can::scheduleTheseDepartments();

			if (!($documentationAccess or $schedulingAccess))
			{
				throw new Exception(Languages::_('ORGANIZER_403'), 403);
			}
		}
		elseif (is_numeric($data['id']))
		{
			if (!Can::document('program', $data['id']))
			{
				throw new Exception(Languages::_('ORGANIZER_403'), 403);
			}
		}
		else
		{
			throw new Exception(Languages::_('ORGANIZER_400'), 400);
		}

		$table = new ProgramsTable;

		if ($table->save($data))
		{
			$model = new Mapping;

			if ($model->saveProgram($table->id))
			{
				return $table->id;
			}
		}

		return false;
	}

	/**
	 * Method to save existing degree programs as copies
	 *
	 * @param   array  $data  the data to be used to create the program when called from the program helper
	 *
	 * @return Boolean
	 * @throws Exception => unauthorized access
	 */
	public function save2copy($data = [])
	{
		if (!Can::documentTheseDepartments())
		{
			throw new Exception(Languages::_('ORGANIZER_403'), 403);
		}

		$data = empty($data) ? Input::getFormItems()->toArray() : $data;
		if (isset($data['id']))
		{
			unset($data['id']);
		}

		$table = new ProgramsTable;

		if (!$table->save($data))
		{
			return false;
		}

		$model = new Mapping;

		return $model->saveProgram($table->id);
	}
}
