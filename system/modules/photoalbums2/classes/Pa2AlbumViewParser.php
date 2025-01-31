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
 * Class Pa2AlbumViewParser
 *
 * @copyright  Daniel Kiesel 2012-2014
 * @author     Daniel Kiesel <daniel@craffft.de>
 * @package    photoalbums2
 */
class Pa2AlbumViewParser extends \Pa2ViewParser
{
    /**
     * objAlbums
     *
     * @var object
     * @access protected
     */
    protected $objAlbums;

    /**
     * generate function.
     *
     * @access protected
     * @return void
     */
    protected function generate()
    {
        $this->Template->intMaxItems = $this->Template->pa2NumberOfAlbums;
        $this->Template->intItemsPerPage = $this->Template->pa2AlbumsPerPage;
        $this->Template->intItemsPerRow = $this->Template->pa2AlbumsPerRow;
        $this->Template->strTemplate = (strlen($this->Template->pa2AlbumViewTemplate) > 0 ? $this->Template->pa2AlbumViewTemplate : 'pa2_wrap');
        $this->Template->strSubtemplate = (strlen($this->Template->pa2AlbumsTemplate) > 0 ? $this->Template->pa2AlbumsTemplate : 'pa2_album');
        $this->Template->intDetailPage = $this->Template->pa2DetailPage;
        $this->Template->albumLightbox = $this->Template->pa2AlbumLightbox;
        $this->Template->showMetaDescriptions = $this->Template->pa2AlbumsShowMetaDescriptions;
        $this->Template->arrMetaFields = $this->Template->pa2AlbumsMetaFields;

        // Image params
        $this->Template->size = $this->Template->pa2AlbumsImageSize;
        $this->Template->imagemargin = $this->Template->pa2AlbumsImageMargin;

        $this->Template->showHeadline = $this->Template->pa2AlbumsShowHeadline;
        $this->Template->showTitle = $this->Template->pa2AlbumsShowTitle;
        $this->Template->showTeaser = $this->Template->pa2AlbumsShowTeaser;
        $this->Template->teaser = $this->cleanRteOutput(\TranslationFieldsHelper::getTranslation($this->Template->pa2Teaser));
        $this->Template->showHeadline = ($this->Template->headline != '' ? $this->Template->showHeadline : false);
        $this->Template->showTeaser = ($this->Template->teaser != '' ? $this->Template->showTeaser : false);

        parent::generate();
    }

    /**
     * compile function.
     *
     * @access protected
     * @return void
     */
    protected function compile()
    {
        // Generate new template object
        $objTemplate = new \FrontendTemplate($this->Template->strTemplate);
        $objTemplate->setData($this->Template->getData());
        $this->Template = $objTemplate;

        // Get album ids from archives
        $objPa2Archive = new \Pa2Archive($this->Template->pa2Archives, $this->Template->getData());
        $arrAllAlbums = $objPa2Archive->getAlbumIds();

        // If there are no albums, show empty template with a message
        if (count($arrAllAlbums) < 1) {
            $this->setEmptyTemplate($GLOBALS['TL_LANG']['MSC']['albumsNotFound']);

            return;
        }

        // Set the pagination
        $objPa2Pagination = new \Pa2Pagination($arrAllAlbums, $this->Template->intMaxItems,
            $this->Template->intItemsPerPage);
        $arrAlbums = $objPa2Pagination->getItems();

        $this->Template->pagination = $objPa2Pagination->getPagination();
        $this->Template->totalItems = $objPa2Pagination->getTotalItems();

        // Get albums of this page as object
        $objPa2Album = new \Pa2Album($arrAlbums, $this->Template->getData());
        $this->objAlbums = $objPa2Album->getAlbums();

        // Call parseAlbums
        $this->parseAlbums();
    }

    /**
     * parseAlbums function.
     *
     * @access private
     * @return void
     */
    private function parseAlbums()
    {
        if (!is_object($this->objAlbums) || $this->objAlbums->count() < 1) {
            $this->setEmptyTemplate($GLOBALS['TL_LANG']['MSC']['albumsNotFound']);

            return;
        }

        // Import
        $this->import('Session');

        // Define vars
        global $objPage;
        $arrItems = array();
        $objAlbums = $this->objAlbums;
        $total = $objAlbums->count();
        $i = 0;

        // Set page session vars to generate backlink in detail page
        $this->Session->set('pa2PageNumber_'.$this->Template->id, (\Input::get('page') ? \Input::get('page') : 1));
        $this->Session->set('pa2PageId_'.$this->Template->id, $objPage->id);

        if ($objAlbums !== null) {
            while ($objAlbums->next()) {
                // Generate subtemplate object
                $objSubtemplate = new \FrontendTemplate($this->Template->strSubtemplate);
                $objSubtemplate->setData($this->Template->getData());

                // Set template variables
                $objSubtemplate->title = strip_tags($objAlbums->title);
                $objSubtemplate->alt = strip_tags($objAlbums->title);
                $objSubtemplate->showTitle = ($objSubtemplate->title != '' ? $objSubtemplate->showTitle : false);
                $objSubtemplate->event = $objAlbums->event;
                $objSubtemplate->place = $objAlbums->place;
                $objSubtemplate->photographer = $objAlbums->photographer;
                $objSubtemplate->description = $objAlbums->description;
                $objSubtemplate->numberOfAllImages = count($objAlbums->arrSortedImageUuids);

                // Call template methods
                $objSubtemplate = $this->addDateToTemplate($objSubtemplate, $objAlbums->startdate, $objAlbums->enddate);
                $objSubtemplate = $this->addSpecificClassesToTemplate($objSubtemplate, $i);
                $objSubtemplate = $this->addLinkToTemplate($objSubtemplate, $objAlbums->current());
                $objSubtemplate = $this->addMetaFieldsToTemplate($objSubtemplate);

                // Add preview image to template
                $objPa2PreviewImage = new \Pa2PreviewImage($objAlbums->current(), $objSubtemplate->pa2PreviewImage);
                $objPa2Image = new \Pa2Image($objPa2PreviewImage->getPreviewImageUuid());
                $objPa2Image->addPa2ImageToTemplate($objSubtemplate);

                // Add album class to the class string
                $objSubtemplate->class .= ($objSubtemplate->class == '') ? $objAlbums->cssClass : ' '.$objAlbums->cssClass;

                // If album lightbox is activated the images will be added to the album template
                $objSubtemplate = $this->albumLightbox($objSubtemplate, $objAlbums->current());

                // Parse subtemplate
                $arrItems[] = $objSubtemplate->parse();

                $i++;
            }
        }

        $this->Template->items = $arrItems;
    }

    /**
     * pa2AlbumLightbox function.
     *
     * @access protected
     * @param  object $objTemplate
     * @param  object $objAlbum
     * @return object
     */
    protected function albumLightbox($objTemplate, $objAlbum)
    {
        if ($objTemplate->albumLightbox) {
            $arrLightboxImages = array();
            $i = 0;

            // Set album id in template
            $objTemplate->albumID = $objAlbum->id.'_'.$this->generateIndividualId();

            // Sort images
            $objPa2ImageSorter = new \Pa2ImageSorter($objAlbum->imageSortType, $objAlbum->images, $objAlbum->imageSort);
            $arrIds = $objPa2ImageSorter->getSortedUuids();

            if ($arrIds > 0) {
                foreach ($arrIds as $intId) {
                    $objPa2Image = new \Pa2Image($intId);
                    $objImage = $objPa2Image->getPa2Image();

                    if ($objImage !== null) {
                        if ($i == 0) {
                            $objTemplate->href = str_replace(' ', '%20', $objImage->path);
                        } else {
                            // Define image template
                            $objImageTemplate = new \FrontendTemplate('pa2_lightbox_image');

                            // Set vars
                            $objImageTemplate->albumID = $objTemplate->albumID;

                            $objImageTemplate->href = str_replace(' ', '%20', $objImage->path);

                            // Add image to template
                            $arrImage = array();
                            $arrImage['size'] = serialize(array(0, 0, 'crop'));
                            $arrImage['imagemargin'] = serialize(array(
                                    'bottom' => '',
                                    'left'   => '',
                                    'right'  => '',
                                    'top'    => '',
                                    'unit'   => '',
                                ));
                            $arrImage['singleSRC'] = 'system/modules/photoalbums2/assets/blank.gif';
                            $arrImage['alt'] = substr(strrchr($element, '/'), 1);

                            $objImageTemplate = $objPa2Image->addPa2ImageToTemplate($objImageTemplate, $arrImage);

                            // Add link title to template
                            $objImageTemplate->title = substr(strrchr($objImage->path, '/'), 1);

                            // Add image template to parent template
                            $arrLightboxImages[] = $objImageTemplate->parse();
                        }
                    }

                    $i++;
                }

                $objTemplate->albumLightboxImages = $arrLightboxImages;
            }
        }

        return $objTemplate;
    }
}
