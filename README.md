# ViteEncore Bundle
This Symfony bundle assists with the integration of [Vite](https://vitejs.dev)
by providing Twig functions to render assets based on Vite's `manifest.json` or
its dev server.

## Requirements
In order to use this bundle, your project has to fulfill the following requirements:

* Symfony `^4.4 || ^5.0`

## Installation
Install the bundle by running:

```
composer require k10r/vite-encore
```

If you have not installed Vite, yet, do so by running:

```
npm install -D vite
```

## Configuration
Configuring the bundle consists of two parts: Vite and Symfony.
The latter part is optional, since this bundle already provides reasonable defaults.

## Vite
Start by creating a `vite.config.js` file inside your project's root directory and
populate it with the following content:

```js
import { defineConfig } from 'vite';
import { resolve } from 'path';

export default defineConfig({
    root: 'assets',
    base: '/dist/',

    build: {
        outDir: resolve(__dirname, 'public/dist'),
        emptyOutDir: true,
        manifest: true,
        target: 'es2018',
        rollupOptions: {
            input: '/js/app.js'
        },
    },

    server: {
        cors: true,
        strictPort: true,
        port: 3000,
    },
});
```

From there on, you can adjust the configuration as you see fit. For more information
on how to configure Vite, see the [official documentation](https://vitejs.dev/config/).

To make the above configuration work with your project, you want to create an
`app.js` inside the `<project-root>/assets/js` directory or adjust the `root`
and `build.rollupOptions.input` options inside the config.

## Symfony
In order to change the default configuration, a `k10_vite_encore.yaml` can be
created inside the `<project-root>/config/packages` directory.

The default configuration is as follows:

```yaml
# config/packages/k10r_vite_encore.yaml
k10r_vite_encore:
    base: /dist/
    server:
        enabled: true
        host: localhost
        port: 3000
        https: false
```

### Options

`base` - string

This option tells Symfony where to find the bundled assets, relative to the 
`public` directory. The value has to be equal to the `root` option inside the
Vite config, as well as match the final path fragment of the `build.outDir` option.

`server.enabled` - bool

This option controls, if the Vite dev server should be used, when the project
is launched with `APP_ENV=dev`. This requires the development server to be started.

`server.host` - string

This options controls, which hostname is used to query assets from the development server.
It should correspond to the IP/hostname on which the development server will be running.

`server.port` - int

This option controls, which port is used to access the development server.
It should correspond to the `server.port` option from the Vite configuration.

`server.https` - bool

This options controls, if the development server should be accessed via https.

## Usage

In order to include your bundled assets, use the following Twig functions for
scripts and styles respectively:

```twig
{{ vite_style_entry('js/app.js') }}
{{ vite_script_entry('js/app.js') }}
```

The `js/app.js` is the entrypoint corresponding to the Vite configuration provided
above. It as to correspond to the `build.rollupOptions.input` value without trailing slashes.

### Using the development server
You can launch the development server by running `npx vite` or creating an
alias script inside your `package.json` to run `vite`.

### Building for production
If you want to build for production, use the command `npx vite build` or do so
by creating an alias script inside your `package.json` to execute `vite build`.

## Changelog
This project adheres to [Semantic Versioning](https://semver.org/).
Please refer to the [CHANGELOG.md](CHANGELOG.md) for detailed changes and
migration instructions.

## License
MIT
