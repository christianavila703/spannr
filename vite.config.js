import fs from 'fs';
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
const path = require('path');

import {homedir} from 'os'
import {resolve} from 'path'

let host = 'spannr.test'

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/sass/admin.scss',
                'resources/sass/app.scss',
                'resources/sass/choices.scss',
                'resources/sass/tinymce.scss',
                'resources/sass/matter-logos.scss',
                'resources/sass/geosearch-autocomplete.scss',
                'resources/js/app.js',
                'resources/js/longevity-stock-chart.js',
                'resources/js/longevity-stock-chart-home.js',
                'resources/js/entity-duplicate-merge.js',
                'resources/vendor/laravel_backup_panel/app.css',
                'resources/vendor/laravel_backup_panel/bootstrap.css',
                'resources/js/company-carousel.js',
                'resources/js/tinymce.js',
                'resources/js/home.js',
                'resources/js/meta-descriptions.js',
                'resources/js/about.js',
                'resources/js/sticky-sidebar.js',
                'resources/js/glossary.js',
                'resources/js/article-carousel.js',
                'resources/js/tinymce-jobs.js',
                'resources/js/job-applicant-location.js',
                'resources/js/recruiting.js',
                'resources/js/job-submission-location.js',
            ],
            refresh: true,
        }),
    ],
    server: detectServerConfig(host),
    resolve: {
        alias: {
            '~bootstrap': path.resolve(__dirname, 'node_modules/bootstrap'),
        }
    },
});

function detectServerConfig(host) {
    let keyPath = resolve(homedir(), `.config/valet/Certificates/${host}.key`)
    let certificatePath = resolve(homedir(), `.config/valet/Certificates/${host}.crt`)

    if (!fs.existsSync(keyPath)) {
        return {}
    }

    if (!fs.existsSync(certificatePath)) {
        return {}
    }

    return {
        hmr: {host},
        host,
        https: {
            key: fs.readFileSync(keyPath),
            cert: fs.readFileSync(certificatePath),
        },
    }
}
