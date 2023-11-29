const defaultTheme = require('tailwindcss/defaultTheme')
const colors = require('tailwindcss/colors')


module.exports = {
    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter var', ...defaultTheme.fontFamily.sans],
            },
            colors: { 
                danger: colors.rose,
                primary: colors.blue,
                success: colors.green,
                warning: colors.yellow,
            }, 
        },
    },
    darkMode: 'class',
    variants: {
        extend: {
            backgroundColor: ['active'],

        }
    },
    content: [
        './app/**/*.php',
        './resources/**/*.html',
        './resources/**/*.js',
        './resources/**/*.jsx',
        './resources/**/*.ts',
        './resources/**/*.tsx',
        './resources/**/*.php',
        './resources/**/*.vue',
        './resources/**/*.twig',
        './resources/**/*.blade.php',
        './app/Filament/**/*.php',
        './resources/views/filament/**/*.blade.php',
        './vendor/filament/**/*.blade.php',
    ],
    plugins: [
        require('@tailwindcss/forms'),
        require('@tailwindcss/typography'),
    ], 
}
