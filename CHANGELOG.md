# Changelog Photoalbums2

## Version 1.1.1 (2024-12-06)

* Fix: Return value of Contao\CoreBundle\File\Metadata::getAlt() must be of the type string, bool returned (beim Aufruf einer Galerie) -> in Pa2Image haben beim Aufruf von \Controller::addImageToTemplate($objTemplate, $arrData, null, null, $objFile); die letzten drei Parameter gefehlt

## Version 1.1.0 (2024-12-06)

* Change: Änderungen wegen translation-fields-bundle
* Add: Tabelle tl_translation_fields, um diese als Dummy vorrätig zu haben
* Add: Model TranslationFieldsModel für den Zugriff auf Tabelle tl_translation_fields
* Change: Felder event, place, photographer, description in tl_photoalbums2_album von int(10) auf varchar(255) bzw. text
* Add: Klasse TranslationFieldsHelper

## Version 1.0.5 (2024-12-06)

* Fix: composer.json nicht valide

## Version 1.0.4 (2024-12-06)

* Fix: Anpassungen auf PHP 8
* Delete: Abhängigkeit schachbulle/translation-fields-bundle, um die Probleme mit TranslationFields zu umgehen

## Version 1.0.3 (2024-12-05)

* Fix: Anpassungen auf PHP 8 nachdem jetzt craffft/translation-fields-bundle aktiv ist
* Change: composer.json Abhängigkeit von crafft/translation-fields-bundle auf schachbulle/translation-fields-bundle geändert

## Version 1.0.2 (2024-12-05)

* Fix: Anpassungen auf PHP 8
* Change: composer.json Abhängigkeit von craffft/contao-translation-fields auf craffft/translation-fields-bundle geändert

## Version 1.0.1 (2024-12-05)

* Fix: composer.json wegen Änderung des Namens der Erweiterung

## Version 1.0.0 (2024-12-05)

Initialversion als Fork von https://github.com/Craffft/contao-photoalbums2

