<?php
/**
 * @package     Organizer
 * @extension   com_organizer
 * @author      James Antrim, <james.antrim@nm.thm.de>
 * @copyright   2020 TH Mittelhessen
 * @license     GNU GPL v.3
 * @link        www.thm.de
 */

namespace Organizer\Fields;

use Organizer\Helpers\Languages;

trait Translated
{
	/**
	 * Method to get the data to be passed to the layout for rendering.
	 *
	 * @return  array
	 */
	protected function getLayoutData()
	{
		if (!empty($this->element['label']))
		{
			$labelConstant          = 'ORGANIZER_' . (string) $this->element['label'];
			$descriptionConstant    = $labelConstant . '_DESC';
			$this->element['label'] = Languages::_($labelConstant);
			$this->description      = Languages::_($descriptionConstant);
		}

		return parent::getLayoutData();
	}
}