Typo3 + Vite.js
====

This extension adds vite.js to your typo3 project!

Setup
-----

Add a `package.json` file to your extension or add the following dependencies to your file

```json
{
    "name": "extension_name",
    "private": true,
    "version": "0.0.1",
    "scripts": {
        "dev": "vite --host 0.0.0.0",
        "build": "vite build",
        "preview": "vite preview"
    },
    "dependencies": {
        "@fullhuman/postcss-purgecss": "^4.1.3",
        "sass": "^1.54.4",
        "vite": "^3.0.7",
        "vite-plugin-pwa": "^0.12.3"
    }
}
```

Add a `postcss.config.js` file to your extension

```js
const path = require('path');
const postCssPurge = require('@fullhuman/postcss-purgecss');

const plugins = [];

if (process.env.NODE_ENV === 'production') {
    plugins.push(
        postCssPurge({
            safelist: [],
            contentFunction: () => {
                let extPath = path.resolve(__dirname + '/Resources/Private')

                return [
                    extPath + '/**/*.html',
                    extPath + '/**/*.js',
                ]
            },
            defaultExtractor(content) {
                return content.match(/[\w-/:]+(?<!:)/g) || []
            }
        })
    );
}

module.exports = {plugins: plugins};
```

Add a `vite.config.js` file to your extension. If you don't use ddev as environment you can remove the `https` object in the config.

You are free to change the input and output paths and the alias. If you change the paths, you also need to change your paths in the typoscript configuration.

```js
import fs from 'fs'
import path from 'path'
import { defineConfig } from 'vite'
import { VitePWA } from 'vite-plugin-pwa'

/** @type {import('vite').UserConfig} */
const config = {
    server: {
        port: 5173,
        https: {
            key: fs.readFileSync('/etc/ssl/certs/master.key'),
            cert: fs.readFileSync('/etc/ssl/certs/master.crt'),
        }
    },
    base: '',
    publicDir: 'fake_dir_so_nothing_gets_copied',
    build: {
        manifest: true,
        outDir: 'Resources/Public',
        rollupOptions: {
            input: [
                'Resources/Private/Frontend/main.js',
            ]
        }
    },
    resolve: {
        alias: [
            {
                find: '@',
                replacement: path.resolve(__dirname + '/Resources/Private/Frontend/')
            }
        ]
    },
    plugins: [
        VitePWA({
            strategies: 'injectManifest',
            srcDir: 'Resources/Private/Frontend/',
            filename: 'sw.js',
            includeAssets: ['/favicon.ico', '/robots.txt', '/images/apple-touch-icon.png'],
            manifest: {
                name: 'Hombre',
                short_name: 'Hombre',
                icons: [
                    {
                        src: '/images/android-chrome-192x192.png',
                        sizes: '192x192',
                        type: 'image/png'
                    },
                    {
                        src: '/images/android-chrome-512x512.png',
                        sizes: '512x512',
                        type: 'image/png'
                    }
                ],
                theme_color: '#ffffff',
                background_color: '#ffffff',
                display: 'standalone',
                orientation: 'portrait'
            }
        }),
        {
            name: 'html',
            handleHotUpdate({file, server}) {
                if (file.endsWith('.html')) {
                    server.ws.send({
                        type: 'full-reload',
                        path: '*'
                    });
                }
            }
        }
    ]
}

export default defineConfig(config)
```

Extend your TypoScript with your configuration. You can use the template setup in the backend or your `setup.typoscript` file for that.

The port need to be the same as in the `vite.config.js`.

```txt
plugin.tx_typo3vite.settings {
    extension_name {
        port = 5173
        out = Resources/Public
        src = Resources/Private/Frontend
    }
}
```

Add the viewhelpers in your page template to use your bundled files. The entry is the filename of the input files from the `vite.config.js`.

```xml
{namespace vite=Crazy252\Typo3Vite\ViewHelpers}

<vite:asset extension="extension_name" entry="main.js" />
<vite:webManifest extension="extension_name" />
```

If you use ddev as environment, you need to extension ddev with your port. Create a file in the `.ddev` folder named `docker-compose.ports.yaml` and add the following content.

```yaml
version: '3.6'

services:
  web:
    ports:
      - "127.0.0.1:5173:5173"
```

And now it's done. Start the dev server in your extension folder via `yarn dev` or other javascript package managers.

After that, you can view your site with the `?no_cache=1` and you got the full power of vite.js in typo3!