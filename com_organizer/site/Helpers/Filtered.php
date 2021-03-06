<?php
/**
 * @package     Organizer
 * @extension   com_organizer
 * @author      James Antrim, <james.antrim@nm.thm.de>
 * @copyright   2020 TH Mittelhessen
 * @license     GNU GPL v.3
 * @link        www.thm.de
 */

namespace Organizer\Helpers;

use JDatabaseQuery;

/**
 * Class contains functions for department filtering.
 */
trait Filtered
{
	/**
	 * Restricts the query by the departmentIDs for which the user has the given access right.
	 *
	 * @param   JDatabaseQuery &$query   the query to modify
	 * @param   string          $alias   the alias being used for the resource table
	 * @param   string          $action  the access right to be filtered against
	 */
	public static function addAccessFilter(&$query, $alias, $action)
	{
		switch ($action)
		{
			case 'document':
				$authorizedDepts = Can::documentTheseDepartments();
				break;
			case 'manage':
				$authorizedDepts = Can::manageTheseDepartments();
				break;
			case 'schedule':
				$authorizedDepts = Can::scheduleTheseDepartments();
				break;
			case 'view':
				$authorizedDepts = Can::viewTheseDepartments();
				break;
		}

		$authorizedDepts = implode(',', $authorizedDepts);
		$query->where("$alias.departmentID IN ($authorizedDepts)");
	}

	/**
	 * Adds a resource filter for a given resource.
	 *
	 * @param   JDatabaseQuery &$query  the query to modify
	 * @param   string          $alias  the alias for the linking table
	 */
	public static function addCampusFilter(&$query, $alias)
	{
		$campusIDs = Input::getFilterIDs('campus');
		if (empty($campusIDs))
		{
			return;
		}

		if (in_array('-1', $campusIDs))
		{
			$query->leftJoin("#__organizer_campuses AS campusAlias ON campusAlias.id = $alias.campusID")
				->where("campusAlias.id IS NULL");
		}
		else
		{
			$campusIDs = implode(',', $campusIDs);
			$query->innerJoin("#__organizer_campuses AS campusAlias ON campusAlias.id = $alias.campusID")
				->where("(campusAlias.id IN ($campusIDs) OR campusAlias.parentID IN ($campusIDs))");
		}
	}

	/**
	 * Adds a selected department filter to the query.
	 *
	 * @param   JDatabaseQuery &$query      the query to be modified
	 * @param   string          $resource   the name of the department associated resource
	 * @param   string          $alias      the alias being used for the resource table
	 * @param   string          $keyColumn  the name of the column holding the association key
	 *
	 * @return void modifies the query
	 */
	public static function addDeptSelectionFilter(&$query, $resource, $alias, $keyColumn = 'id')
	{
		$departmentIDs = Input::getFilterIDs('department');
		if (empty($departmentIDs))
		{
			return;
		}

		$tableWithAlias = '#__organizer_department_resources AS drAlias';
		if (in_array('-1', $departmentIDs))
		{
			$query->leftJoin("$tableWithAlias ON drAlias.{$resource}ID = $alias.$keyColumn")
				->where("drAlias.id IS NULL");
		}
		else
		{
			$query->innerJoin("$tableWithAlias ON drAlias.{$resource}ID = $alias.$keyColumn")
				->where("drAlias.departmentID IN (" . implode(',', $departmentIDs) . ")");
		}
	}

	/**
	 * Adds a resource filter for a given resource.
	 *
	 * @param   JDatabaseQuery &$query          the query to modify
	 * @param   string          $resource       the name of the resource associated
	 * @param   string          $newAlias       the alias for any linked table
	 * @param   string          $existingAlias  the alias for the linking table
	 */
	public static function addResourceFilter(&$query, $resource, $newAlias, $existingAlias)
	{
		$resourceIDs = Input::getFilterIDs($resource);
		if (empty($resourceIDs))
		{
			return;
		}

		$table = OrganizerHelper::getPlural($resource);
		if (in_array('-1', $resourceIDs))
		{
			$query->leftJoin("#__organizer_$table AS $newAlias ON $newAlias.id = $existingAlias.{$resource}ID")
				->where("$newAlias.id IS NULL");
		}
		else
		{
			$query->innerJoin("#__organizer_$table AS $newAlias ON $newAlias.id = $existingAlias.{$resource}ID")
				->where("$newAlias.id IN (" . implode(',', $resourceIDs) . ")");
		}
	}
}
