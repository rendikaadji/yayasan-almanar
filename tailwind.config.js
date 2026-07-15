import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            colors: {
                brand: {
                    sage: '#2C4F3E',
                    teal: '#1B3236',
                    gold: '#C7A870',
                    stone: '#F5F5F2',
                    slate: '#EBEAE4',
                    navy: '#1E293B',
                    moss: '#5F7D6D',
                },
            },
            fontFamily: {
                sans: ['"Plus Jakarta Sans"', ...defaultTheme.fontFamily.sans],
                display: ['Forum', 'serif'],
                utility: ['Outfit', 'sans-serif'],
            },
        },
    },

    plugins: [forms],
};
