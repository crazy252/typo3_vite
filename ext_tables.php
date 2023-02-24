<?php

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

defined('TYPO3_MODE') || die('Access denied.');

ExtensionManagementUtility::addStaticFile(
    'typo3_vite',
    'Configuration/TypoScript',
    'TYPO3 Vite'
);
