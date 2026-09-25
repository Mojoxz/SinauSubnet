import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    darkMode: 'class',

    theme: {
        extend: {
            fontFamily: {
                // IBM Plex Sans — antarmuka utama (self-hosted via @fontsource)
                sans: ['"IBM Plex Sans"', ...defaultTheme.fontFamily.sans],
                // IBM Plex Mono — alamat IP, subnet, biner, kode (self-hosted)
                mono: ['"IBM Plex Mono"', ...defaultTheme.fontFamily.mono],
            },

            // Token warna status penilaian — referensi tambahan di Tailwind config
            // (token utama tetap didefinisikan di App\Enums\StatusPenilaian)
            colors: {
                status: {
                    final:           { DEFAULT: '#059669', light: '#d1fae5', border: '#6ee7b7' },
                    menunggu_ai:     { DEFAULT: '#D97706', light: '#fef3c7', border: '#fcd34d' },
                    dinilai_ai:      { DEFAULT: '#2563EB', light: '#dbeafe', border: '#93c5fd' },
                    divalidasi_guru: { DEFAULT: '#0D9488', light: '#ccfbf1', border: '#5eead4' },
                    dikoreksi_guru:  { DEFAULT: '#7C3AED', light: '#ede9fe', border: '#c4b5fd' },
                    perlu_manual:    { DEFAULT: '#E11D48', light: '#ffe4e6', border: '#fda4af' },
                },
            },
        },
    },

    plugins: [forms],
};
