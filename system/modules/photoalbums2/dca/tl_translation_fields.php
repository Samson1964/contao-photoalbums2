<?php

/*
 * This file is part of the Translation Fields Bundle.
 *
 * (c) Daniel Kiesel <https://github.com/iCodr8>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

\System::loadLanguageFile('tl_translation_fields');

/**
 * Palettes
 */
$GLOBALS['TL_DCA']['tl_translation_fields'] = array
(
    // Config
    'config'   => array
    (
        'dataContainer'    => 'Table',
        'enableVersioning' => true,
        'sql'              => array
        (
            'keys' => array
            (
                'id'           => 'primary',
                'fid,language' => 'unique',
                'fid'          => 'index'
            )
        )
    ),

    // List
    'list'     => array
    (
        'sorting'           => array
        (
            'mode'        => 2,
            'fields'      => array('language'),
            'panelLayout' => 'filter;sort,search'
        ),
        'label'             => array
        (
            'fields'      => array('language', 'fid', 'content'),
            'showColumns' => true
        ),
        'global_operations' => array
        (
            'all' => array
            (
                'label'      => &$GLOBALS['TL_LANG']['MSC']['all'],
                'href'       => 'act=select',
                'class'      => 'header_edit_all',
                'attributes' => 'onclick="Backend.getScrollOffset()" accesskey="e"'
            )
        ),
        'operations'        => array
        (
            'edit'   => array
            (
                'label' => &$GLOBALS['TL_LANG']['tl_content']['edit'],
                'href'  => 'act=edit',
                'icon'  => 'edit.gif'
            ),
            'delete' => array
            (
                'label'      => &$GLOBALS['TL_LANG']['tl_content']['delete'],
                'href'       => 'act=delete',
                'icon'       => 'delete.gif',
                'attributes' => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"'
            ),
            'show'   => array
            (
                'label' => &$GLOBALS['TL_LANG']['tl_content']['show'],
                'href'  => 'act=show',
                'icon'  => 'show.gif'
            )
        ),
    ),

    // Palettes
    'palettes' => array
    (
        'default' => '{translation_legend},fid,language,content'
    ),

    // Fields
    'fields'   => array
    (
        'id'       => array
        (
            'sql' => "int(10) unsigned NOT NULL auto_increment"
        ),
        'tstamp'   => array
        (
            'inputType' => 'text',
            'sql'       => "int(10) unsigned NOT NULL default '0'"
        ),
        'fid'      => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_translation_fields']['fid'],
            'exclude'   => true,
            'search'    => true,
            'sorting'   => true,
            'inputType' => 'text',
            'eval'      => array('tl_class' => 'w50'),
            'sql'       => "int(10) unsigned NOT NULL default '0'"
        ),
        'language' => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_translation_fields']['language'],
            'exclude'   => true,
            'filter'    => true,
            'sorting'   => true,
            'inputType' => 'select',
            'options'   => \System::getLanguages(),
            'eval'      => array('chosen' => true, 'tl_class' => 'w50'),
            'sql'       => "varchar(5) NOT NULL default ''"
        ),
        'content'  => array
        (
            'label'     => &$GLOBALS['TL_LANG']['tl_translation_fields']['content'],
            'exclude'   => true,
            'search'    => true,
            'inputType' => 'textarea',
            'eval'      => array('mandatory' => true),
            'sql'       => "text NOT NULL"
        )
    )
);
