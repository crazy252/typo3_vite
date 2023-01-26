<?php

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

defined('TYPO3_MODE') || die('Access denied.');

ExtensionManagementUtility::addStaticFile(
    'hombre_website',
    'Configuration/TypoScript',
    'Hombre Website'
);
