<?php

namespace Crazy252\Typo3Vite\ViewHelpers;

use Crazy252\Typo3Vite\Utility\Utility;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class ReactViewHelper extends AbstractViewHelper
{
    public function initializeArguments()
    {
        $this->registerArgument(
            'extension',
            'string',
            'Name of the extension folder name',
            true,
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

        $configurationManager = GeneralUtility::makeInstance(ConfigurationManager::class);
        $typoScript = $configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT,
            'typo3_vite'
        );

        $settings = $typoScript['plugin.']['tx_typo3vite.']['settings.'][$extension . '.'] ?? [];

        $domainWithPort = Utility::viteDevServerHost($settings);
        $viteDevServerRunning = Utility::viteDevServerRunning($settings);

        if ($viteDevServerRunning) {
            $code = 'import RefreshRuntime from \'' . $domainWithPort . '/@react-refresh\'
RefreshRuntime.injectIntoGlobalHook(window)
window.$RefreshReg$ = () => {}
window.$RefreshSig$ = () => (type) => type
window.__vite_plugin_react_preamble_installed__ = true';

            $pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);
            $pageRenderer->addJsInlineCode('typo3_vite:reactRefresh', $code, false, true);
        }
    }
}
