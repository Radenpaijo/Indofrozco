import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: ['class'],
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.jsx',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Outfit', 'Inter', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                border: 'var(--border, #cbd5e1)',
                input: 'var(--input, #cbd5e1)',
                ring: 'var(--ring, #0f172a)',
                background: 'var(--background, #ffffff)',
                foreground: 'var(--foreground, #0f172a)',
                primary: {
                    DEFAULT: 'var(--primary, #0f172a)',
                    foreground: 'var(--primary-foreground, #f8fafc)',
                },
                secondary: {
                    DEFAULT: 'var(--secondary, #f1f5f9)',
                    foreground: 'var(--secondary-foreground, #0f172a)',
                },
                destructive: {
                    DEFAULT: 'var(--destructive, #ef4444)',
                    foreground: 'var(--destructive-foreground, #f8fafc)',
                },
                muted: {
                    DEFAULT: 'var(--muted, #f1f5f9)',
                    foreground: 'var(--muted-foreground, #64748b)',
                },
                accent: {
                    DEFAULT: 'var(--accent, #f1f5f9)',
                    foreground: 'var(--accent-foreground, #0f172a)',
                },
                popover: {
                    DEFAULT: 'var(--popover, #ffffff)',
                    foreground: 'var(--popover-foreground, #0f172a)',
                },
                card: {
                    DEFAULT: 'var(--card, #ffffff)',
                    foreground: 'var(--card-foreground, #0f172a)',
                },
            }
        },
    },

    plugins: [forms],
};
