const colors = require('tailwindcss/colors')

module.exports = {
    purge: {
        enabled: false,
        content: ['./assets/**/*.html',
                './assets/**/*.vue',
                './assets/**/*.jsx',
                './templates/**/*.html',
                './templates/**/*.twig']
    },
    darkMode: 'class', // or 'media' or 'class'
    theme: {
        extend: {
            height: {
                screen34: '75vh',
            },
            keyframes: {

                heartbeat: {

                    '0%, 100%': {
                        transform: 'scale(1)',
                        'transform-origin': 'center center',
                        'animation-timing-function': 'ease-out'
                    },
                    '10%': {
                        transform: 'scale(0.91)',
                        'animation-timing-function': 'ease-in'
                    },
                    '17%': {
                        transform: 'scale(0.98)',
                        'animation-timing-function': 'ease-out'
                    },
                    '33%': {
                        transform: 'scale(0.87)',
                        'animation-timing-function': 'ease-in'
                    },
                    '45%': {
                        transform: 'scale(1)',
                        'animation-timing-function': 'ease-out'
                    }
                }
            },
            animation: {
                heartbeat: 'heartbeat 2.5s ease-in-out infinite both',
            },
            spacing: {
                128: '32rem',
            },
        },
        colors: {
            extend: {},
            transparent: 'transparent',
            current: 'currentColor',
            black: colors.black,
            white: colors.white,
            gray: colors.warmGray,
            blue: colors.lightBlue,
            red: colors.red,
            yellow: colors.amber,
            pink: colors.pink,
            green: colors.teal,
            orange: colors.orange,
            primary: '#f9ce1c',
        },
        fontFamily: {
            display: ['Tenor Sans', 'Georgia', 'serif'],
            body: ['Inter', 'system-ui', 'sans-serif'],
            sans: ['Noto Sans','Open Sans', 'Oswald', 'Ubuntu', 'ui-sans-serif', 'system-ui'],
            serif: ['Noto Serif','Georgia', 'Cambria', "Times New Roman", 'Times', 'ui-serif'],
        }
    },
    variants: {
        extend: {},
    },
    plugins: [
        require('@tailwindcss/typography'),
        require('@tailwindcss/forms')
    ],
}
