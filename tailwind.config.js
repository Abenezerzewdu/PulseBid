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
            fontFamily: {
                sans: ['Manrope', ...defaultTheme.fontFamily.sans],
                display: ['Space Grotesk', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                surface: {
                    lowest: '#000000',
                    DEFAULT: '#0e0e0e',
                    container: '#1a1919',
                    bright: '#2c2c2c',
                    variant: '#1e1e1e',
                },
                primary: {
                    DEFAULT: '#a5ffb8',
                    container: '#00fd84',
                    on: '#0e0e0e',
                },
                secondary: {
                    DEFAULT: '#53a0ff',
                    on: '#0e0e0e',
                },
                vivid: '#FF0099',
                outline: {
                    variant: 'rgba(255,255,255,0.15)',
                },
            },
            borderRadius: {
                xl: '1rem',
                '2xl': '1.5rem',
                '3xl': '2rem',
                '4xl': '3rem',
            },
            boxShadow: {
                ambient: '0 0 48px -4px rgba(0,0,0,0.06)',
                glow: '0 0 20px rgba(165,255,184,0.3)',
                'glow-blue': '0 0 20px rgba(83,160,255,0.3)',
            },
            animation: {
                pulse: 'pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                'fade-in': 'fadeIn 0.3s ease-out',
            },
            keyframes: {
                fadeIn: {
                    '0%': { opacity: '0', transform: 'translateY(8px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
            },
        },
    },

    plugins: [forms],
};
