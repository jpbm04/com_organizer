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

use Organizer\Helpers\Buildings;
use Organizer\Helpers\HTML;

/**
 * Class creates a form field for building selection.
 */
class BuildingsField extends OptionsField
{
	/**
	 * @var  string
	 */
	protected $type = 'Buildings';

	/**
	 * Returns a select box where stored buildings can be chosen
	 *
	 * @return array  the available buildings
	 */
	protected function getOptions()
	{
		$defaultOptions = parent::getOptions();
		$options        = Buildings::getOptions();

		return array_merge($defaultOptions, $options);
	}
}
