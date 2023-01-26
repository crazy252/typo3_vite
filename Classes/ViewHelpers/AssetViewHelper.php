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

class AssetViewHelper extends AbstractViewHelper
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
            'entry',
            'string',
            'Path to file in the extension folder',
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
        $entry = $arguments['entry'];
        $configFileName = $arguments['config'];

        $configurationManager = GeneralUtility::makeInstance(ConfigurationManager::class);
        $typoScript = $configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT,
            'typo3_vite'
        );

        $settings = $typoScript['plugin.']['tx_typo3vite.']['settings.'][$extension . '.'] ?? [];

        $extensionPath = ExtensionManagementUtility::extPath($extension);
        $outPath = $settings['out'] ?? Utility::viteOutPath($extensionPath, $configFileName);
        $srcPath = $settings['src'] ?? null;

        $domainWithPort = Utility::viteDevServerHost($settings);
        $viteDevServerRunning = Utility::viteDevServerRunning($settings);
        $pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);

        if ($viteDevServerRunning) {
            $pageRenderer->addJsFile($domainWithPort . '/@vite/client', 'module');
            $pageRenderer->addJsFile($domainWithPort . '/' . $srcPath . '/' . $entry, 'module');
        }

        if (!$viteDevServerRunning and $outPath and $srcPath) {
            $files = Utility::viteManifestFile($extension, $extensionPath, $outPath, $srcPath, $entry);
            foreach ($files as $file) {
                if (preg_match('/\.js$/', $file)) {
                    $pageRenderer->addJsFile($file);
                }
                if (preg_match('/\.css$/', $file)) {
                    $pageRenderer->addCssFile($file);
                }
            }
        }
    }
}
