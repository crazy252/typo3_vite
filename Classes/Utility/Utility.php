<?php

namespace Crazy252\Typo3Vite\Utility;

use Exception;
use TYPO3\CMS\Core\Http\RequestFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class Utility
{
    /**
     * @param string $extensionPath
     * @param string $configFileName
     * @return string|null
     */
    public static function viteOutPath(string $extensionPath, string $configFileName): ?string
    {
        $configFilePath = $extensionPath . $configFileName;
        $configFileContent = file_get_contents($configFilePath);

        preg_match_all('/outDir:\s?(.+),/m', $configFileContent, $matches, PREG_SET_ORDER);

        if (empty($matches[0][1])) {
            return null;
        }

        return preg_replace('/\/?(\'|")\/?/m', '', $matches[0][1]);
    }

    public static function viteManifestExists(string $extensionPath, string $outPath): bool
    {
        return file_exists($extensionPath . $outPath . '/manifest.json');
    }

    /**
     * @param string $extension
     * @param string $extensionPath
     * @param string $outPath
     * @param string $srcPath
     * @param string $entry
     * @return array
     */
    public static function viteManifestFile(string $extension, string $extensionPath, string $outPath, string $srcPath, string $entry): array
    {
        if (!self::viteManifestExists($extensionPath, $outPath)) {
            return [];
        }

        $manifestPath = $extensionPath . $outPath . '/manifest.json';
        $outputDir = 'EXT:' . $extension . '/' . $outPath . '/';

        $assets = [];
        foreach (json_decode(file_get_contents($manifestPath)) as $item) {
            if ($item->src != $srcPath . '/' . $entry) {
                continue;
            }

            foreach ($item->imports ?? [] as $file) {
                $assets[] = $outputDir . $file;
            }
            foreach ($item->css ?? [] as $file) {
                $assets[] = $outputDir . $file;
            }
            $assets[] = $outputDir . $item->file;
        }

        return $assets;
    }

    /**
     * @param array $settings
     * @return string
     */
    public static function viteDevServerHost(array $settings): string
    {
        $domain = substr(GeneralUtility::getIndpEnv('TYPO3_SITE_URL'), 0, -1);
        if (!empty($settings['domain'])) {
            $domain = $settings['domain'];
        }

        return preg_replace('/\/+$/m', '', $domain) . ':' . ($settings['port'] ?? 3000);
    }

    /**
     * @param array $settings
     * @return bool
     */
    public static function viteDevServerRunning(array $settings): bool
    {
        $requestFactory = GeneralUtility::makeInstance(RequestFactory::class);
        
        $domain = $settings['domain'] ?? 'https://127.0.0.1';
        $port = $settings['port'] ?? 3000;
        $uri = $settings['uri'] ?? '/@vite/client';

        $timeout = $settings['timeout'] ?? 1.0;
        $verify = $settings['verify'] ?? false;

        try {
            $requestFactory->request($domain . ':' . $port . $uri, 'GET', [
                'timeout' => $timeout,
                'verify' => $verify,
            ]);
        } catch (Exception $e) {
            return false;
        }

        return true;
    }
}
