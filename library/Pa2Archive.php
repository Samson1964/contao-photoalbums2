<?php

/**
 * Extension for Contao Open Source CMS
 *
 * Copyright (c) 2012-2014 Daniel Kiesel
 *
 * @package Photoalbums2
 * @link    https://github.com/icodr8/contao-photoalbums
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */


/**
 * Namespace
 */
namespace Photoalbums2;


/**
 * Class Pa2Archive
 *
 * @copyright  Daniel Kiesel 2012-2014
 * @author     Daniel Kiesel <https://github.com/icodr8>
 * @package    photoalbums2
 */
class Pa2Archive extends \Pa2Lib
{

	/**
	 * sortOut function.
	 *
	 * @access protected
	 * @return void
	 */
	protected function sortOut()
	{
		if (count($this->items) > 0)
		{
			$this->import('FrontendUser', 'User');

			$objItems = \Photoalbums2ArchiveModel::findMultipleByIds($this->items);

			$arrItems = array();

			if ($objItems !== null)
			{
				while ($objItems->next())
				{
					if ($objItems->protected)
					{
						if (!FE_USER_LOGGED_IN)
						{
							continue;
						}

						$arrUsers = deserialize($objItems->users);
						$arrGroups = deserialize($objItems->groups);

						// Check users and groups
						if ((!is_array($arrUsers) || count($arrUsers) < 1 || count(array_intersect($arrUsers, array($this->User->id))) < 1) && (!is_array($arrGroups) || count($arrGroups) < 1 || count(array_intersect($arrGroups, $this->User->groups)) < 1))
						{
							continue;
						}
					}

					$arrItems[] = $objItems->id;
				}
			}

			$this->items = $arrItems;
		}
	}


	/**
	 * getArchiveIds function.
	 *
	 * @access public
	 * @return array
	 */
	public function getArchiveIds()
	{
		return $this->items;
	}


	/**
	 * getArchives function.
	 *
	 * @access public
	 * @return object
	 */
	public function getArchives()
	{
		if (count($this->items) > 0)
		{
			return \Photoalbums2ArchiveModel::findMultipleByIds($this->items);
		}

		return null;
	}


	/**
	 * getAlbumIds function.
	 *
	 * @access public
	 * @return array
	 */
	public function getAlbumIds()
	{
		$arrAlbumIds = array();
		$objAlbums = \Photoalbums2AlbumModel::findAlbumsByMultipleArchives($this->items);

		// Return null if albums is not an object
		if ($objAlbums === null)
		{
			return null;
		}

		while ($objAlbums->next())
		{
			$arrAlbumIds[] = $objAlbums->id;
		}

		if (isset($this->pa2AlbumSortType) && isset($this->pa2AlbumSort))
		{
			$objPa2AlbumSorter = new \Pa2AlbumSorter($this->pa2AlbumSortType, $arrAlbumIds, $this->pa2AlbumSort);
			$arrAlbumIds = $objPa2AlbumSorter->getSortedIds();

			if ($arrAlbumIds === false)
			{
				return null;
			}
		}

		$objPa2Album = new \Pa2Album($arrAlbumIds, $this->getData());

		return $objPa2Album->getAlbumIds();
	}


	/**
	 * getAlbums function.
	 *
	 * @access public
	 * @return object
	 */
	public function getAlbums()
	{
		$objPa2Album = new \Pa2Album($this->getAlbumIds(), $this->getData());

		return $objPa2Album->getAlbums();
	}
}