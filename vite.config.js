import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input:[
                    'resources/css/app.css', // 既存のCSS
                    'resources/css/user.css', // ★ これを追加
                    'resources/js/app.js'
                ],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
