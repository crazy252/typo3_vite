<?php

declare(strict_types=1);

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

ExtensionManagementUtility::addStaticFile(
    'typo3_vite',
    'Configuration/TypoScript',
    'TYPO3 Vite'
);
