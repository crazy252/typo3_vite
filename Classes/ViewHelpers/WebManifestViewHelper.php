<?php

namespace Crazy252\Typo3Vite\ViewHelpers;

use Crazy252\Typo3Vite\Utility\Utility;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * @deprecated Will be removed in the next version
 */
class WebManifestViewHelper extends AbstractViewHelper
{
    public function initializeArguments()
    {
        $this->registerArgument(
            'extension',
            'string',
            'Name of the extension folder name',
            true,
        );
        $this->registerArgument(
            'config',
            'string',
            'Name of vite config file',
            false,
            'vite.config.js',
        );
    }

    /**
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     * @throws InvalidConfigurationTypeException
     */
    public static function renderStatic(array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext)
    {
        $extension = $arguments['extension'];
        $configFileName = $arguments['config'];

        $configurationManager = GeneralUtility::makeInstance(ConfigurationManager::class);
        $typoScript = $configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT,
            'typo3_vite'
        );

        $settings = $typoScript['plugin.']['tx_typo3vite.']['settings.'][$extension . '.'] ?? [];

        $extensionPath = ExtensionManagementUtility::extPath($extension);
        $outPath = $settings['out'] ?? Utility::viteOutPath($extensionPath, $configFileName);

        $viteDevServerRunning = Utility::viteDevServerRunning($settings);
        $pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);

        $webManifestPath = $extensionPath . $outPath . '/manifest.webmanifest';

        if (!$viteDevServerRunning and file_exists($webManifestPath)) {
            $path = 'EXT:' . $extension . '/' . $outPath . '/manifest.webmanifest';
            $pageRenderer->addCssFile($path, 'manifest');
        }
    }
}
