/** @type {import('tailwindcss').Config} */
const colors = require('tailwindcss/colors')
const defaultTheme = require('tailwindcss/defaultTheme')

module.exports = {
    darkMode: 'class',
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
    ],
    theme: {
        extend: {
            colors: {
                primary: colors.indigo,
                success: colors.emerald,
                warning: colors.amber,
                error: colors.rose,
                info: colors.sky,
                surface: {
                    50: '#fafafa',
                    100: '#f4f4f5',
                    200: '#e4e4e7',
                    300: '#d4d4d8',
                    400: '#a1a1aa',
                    500: '#71717a',
                    600: '#52525b',
                    700: '#3f3f46',
                    800: '#27272a',
                    900: '#18181b',
                },
            },
            fontFamily: {
                sans: ['Inter var', ...defaultTheme.fontFamily.sans],
            },
            spacing: {
                '128': '32rem',
                '144': '36rem',
            },
            borderRadius: {
                '4xl': '2rem',
            },
            minHeight: {
                'screen-75': '75vh'
            },
            fontSize: {
                '55': '55rem',
            },
            opacity: {
                '65': '.65',
            },
            zIndex: {
                '2': 2,
                '3': 3,
            },
            inset: {
                '-100': '-100%',
                '-225-px': '-225px',
                '-160-px': '-160px',
                '-150-px': '-150px',
                '-94-px': '-94px',
                '-50-px': '-50px',
                '-29-px': '-29px',
                '-20-px': '-20px',
                '25-px': '25px',
                '40-px': '40px',
                '95-px': '95px',
                '145-px': '145px',
                '195-px': '195px',
                '210-px': '210px',
                '260-px': '260px',
            },
            height: {
                '95-px': '95px',
                '70-px': '70px',
                '350-px': '350px',
                '500-px': '500px',
                '600-px': '600px',
            },
            maxHeight: {
                '860-px': '860px',
            },
            maxWidth: {
                '100-px': '100px',
                '120-px': '120px',
                '150-px': '150px',
                '180-px': '180px',
                '200-px': '200px',
                '210-px': '210px',
                '580-px': '580px',
            },
            minWidth: {
                '140-px': '140px',
                '48': '12rem',
            },
            backgroundSize: {
                full: '100%',
            },
            boxShadow: {
                'soft-xl': '0 20px 27px 0 rgba(0, 0, 0, 0.05)',
                'soft-2xl': '0 25px 50px -12px rgba(0, 0, 0, 0.05)',
                'elegant': '0 0 50px 0 rgba(0, 0, 0, 0.1)',
                'glass': '0 8px 32px 0 rgba(31, 38, 135, 0.07)',
            },
            transitionProperty: {
                'height': 'height',
                'spacing': 'margin, padding',
            }
        },
    },
    plugins: [
        require('@tailwindcss/forms'),
        require('@tailwindcss/typography'),
    ],
}; 