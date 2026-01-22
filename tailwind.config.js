import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    safelist: [
        // Badge colors - untuk server-rendered HTML dari BadgeHelper
        'bg-[#FEF9C2]',
        'text-[#D08700]',
        'bg-[#DBEAFE]',
        'text-[#193CB8]',
        'bg-[#DCFCE7]',
        'text-[#008236]',
        'bg-[#FFE2E2]',
        'text-[#C10007]',
        // Badge sizes
        'px-2',
        'py-1',
        'px-3',
        'py-1.5',
        'px-4',
        'py-2',
        'text-xs',
        'text-sm',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Open Sans', 'sans-serif'],
            },
        },
    },

    plugins: [forms],
};
