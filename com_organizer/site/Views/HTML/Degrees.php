<?php
/**
 * @package     Organizer
 * @extension   com_organizer
 * @author      James Antrim, <james.antrim@nm.thm.de>
 * @copyright   2020 TH Mittelhessen
 * @license     GNU GPL v.3
 * @link        www.thm.de
 */

namespace Organizer\Views\HTML;

use Joomla\CMS\Toolbar\Toolbar;
use Organizer\Helpers\Can;
use Organizer\Helpers\HTML;
use Organizer\Helpers\Languages;

/**
 * Class loads persistent information a filtered set of degrees into the display context.
 */
class Degrees extends ListView
{
	protected $rowStructure = ['checkbox' => '', 'name' => 'link', 'abbreviation' => 'link', 'code' => 'link'];

	/**
	 * Method to generate buttons for user interaction
	 *
	 * @return void
	 */
	protected function addToolBar()
	{
		HTML::setTitle(Languages::_('ORGANIZER_DEGREES'), 'info-cap');
		$toolbar = Toolbar::getInstance();
		$toolbar->appendButton('Standard', 'new', Languages::_('ORGANIZER_ADD'), 'degrees.add', false);
		$toolbar->appendButton('Standard', 'edit', Languages::_('ORGANIZER_EDIT'), 'degrees.edit', true);
		$toolbar->appendButton(
			'Confirm',
			Languages::_('ORGANIZER_DELETE_CONFIRM'),
			'delete',
			Languages::_('ORGANIZER_DELETE'),
			'degrees.delete',
			true
		);
	}

	/**
	 * Function determines whether the user may access the view.
	 *
	 * @return bool true if the use may access the view, otherwise false
	 */
	protected function allowAccess()
	{
		return Can::administrate();
	}

	/**
	 * Function to set the object's headers property
	 *
	 * @return void sets the object headers property
	 */
	public function setHeaders()
	{
		$ordering  = $this->state->get('list.ordering');
		$direction = $this->state->get('list.direction');
		$headers   = [
			'checkbox'     => '',
			'name'         => HTML::sort('NAME', 'name', $direction, $ordering),
			'abbreviation' => HTML::sort('ABBREVIATION', 'abbreviation', $direction, $ordering),
			'code'         => HTML::sort('DEGREE_CODE', 'code', $direction, $ordering)
		];

		$this->headers = $headers;
	}
}
