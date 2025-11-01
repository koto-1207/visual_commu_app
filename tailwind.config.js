import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['M PLUS Rounded 1c', 'Hiragino Sans', 'Hiragino Kaku Gothic ProN', 'Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                'pic-bg': '#f2faf9',      // 背景色（ロゴのピンクを薄くした色）
                'pic-mint': '#a2d9ce',    // ミントグリーン（ロゴの色）
                'pic-pink': '#f7b2b7',    // ピンク（ロゴの色）
                'pic-mint-dark': '#5eb89f',    // ミントグリーン（濃いめ）
                'pic-pink-dark': '#e8868d',    // ピンク（濃いめ）
            },
            borderRadius: {
                'card': '1.5rem',
            },
        },
    },

    plugins: [forms],
};
