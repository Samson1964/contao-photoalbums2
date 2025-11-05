<?php

/**
 * Extension for Contao Open Source CMS
 *
 * Copyright (c) 2012-2014 Daniel Kiesel
 *
 * @package Photoalbums2
 * @link    https://github.com/craffft/contao-photoalbums
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */

/**
 * Namespace
 */
namespace Photoalbums2;

/**
 * Class Pa2Empty
 *
 * @copyright  Daniel Kiesel 2012-2014
 * @author     Daniel Kiesel <daniel@craffft.de>
 * @package    photoalbums2
 */
class TranslationFieldsHelper
{
	/**
	 * Wert aus Translation-Tabelle laden und umwandeln
	 * @param mixed
	 * @return mixed
	 */
	public static function getTranslation($varValue)
	{
		$intValue = (int)$varValue;

		if($varValue == $intValue)
		{
			$objRecord = \Database::getInstance()->prepare("SELECT * FROM tl_translation_fields WHERE fid=? AND language=?")
			                                     ->execute($varValue, 'de');
			if($objRecord->numRows)
			{
				return $objRecord->content;
			}
			else return $varValue;
		}
		else
		{
			// Wert unverändert zurückgeben
			return $varValue == 0 ? '' : $varValue;
		}
	}

}
