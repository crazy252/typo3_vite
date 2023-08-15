<?php

if (!(defined('TYPO3_MODE') || defined('TYPO3'))) {
    die('Access denied.');
}

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

ExtensionManagementUtility::addStaticFile(
    'typo3_vite',
    'Configuration/TypoScript',
    'TYPO3 Vite'
);
