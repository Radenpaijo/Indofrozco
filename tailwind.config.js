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
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                indigo: {
                    50: '#F0F9FF',   // sky-50
                    100: '#E0F2FE',  // sky-100
                    200: '#BAE6FD',  // sky-200
                    300: '#7DD3FC',  // sky-300
                    400: '#38BDF8',  // sky-400
                    500: '#0EA5E9',  // sky-500
                    600: '#0284C7',  // sky-600
                    700: '#0369A1',  // sky-700
                    800: '#075985',  // sky-800
                    900: '#0C4A6E',  // sky-900
                    950: '#082F49',  // sky-950
                },
                violet: {
                    50: '#ECFEFF',   // cyan-50
                    100: '#CFFAFE',  // cyan-100
                    200: '#A5F3FC',  // cyan-200
                    300: '#67E8F9',  // cyan-300
                    400: '#22D3EE',  // cyan-400
                    500: '#06B6D4',  // cyan-500
                    600: '#0891B2',  // cyan-600
                    700: '#0E7490',  // cyan-700
                    800: '#155E75',  // cyan-800
                    900: '#164E63',  // cyan-900
                    950: '#083344',  // cyan-950
                }
            }
        },
    },

    plugins: [forms],
};
