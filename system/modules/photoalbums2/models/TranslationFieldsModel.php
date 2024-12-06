<?php
namespace Photoalbums2;

//use Contao\Model;

/**
 * add properties for IDE support
 * 
 * @property string $hash
 */
class TranslationFieldsModel extends \Model
{
	protected static $strTable = 'tl_translation_fields';
	
	// if you have logic you need more often, you can implement it here
	public function setHash()
	{
		$this->hash = md5($this->id);
	}
}
