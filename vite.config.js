import fs from 'fs';
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import { homedir } from 'os';
import { resolve } from 'path';

const host = 'sokjetech.test';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/js/app.js',
            ],
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    server: detectServerConfig(host),
});

function detectServerConfig (host) {
    const keyPath = resolve(homedir(), `.config/valet/Certificates/${host}.key`);
    const certificatePath = resolve(homedir(), `.config/valet/Certificates/${host}.crt`);

    if (!fs.existsSync(keyPath)) {
        return {};
    }

    if (!fs.existsSync(certificatePath)) {
        return {};
    }

    return {
        hmr: { host },
        host,
        https: {
            key: fs.readFileSync(keyPath),
            cert: fs.readFileSync(certificatePath),
        },
    };
}
