const production = process.env.NODE_ENV == 'production';

module.exports = {
  corePlugins: {
    // do not add global styles:
    preflight: production?true:true,
  },
  content: [
    './public/index.html', './**/*.svelte'
  ],
  theme: {
    extend: {
      screens: {
        'xs': {
          'max': '639px'
        },
        'print': {
          'raw': 'print'
        },
      },
      keyframes: {
        wobble: {
          '0%, 100%': { transform: 'translateY(0)' , 'animation-timing-function': 'cubic-bezier(0, 0, 0.2, 1) '},
          '50%': { transform: 'translateY(-4%)', 'animation-timing-function': 'cubic-bezier(0.8, 0, 1, 1)'},
        }
      },
      animation: {
        'wobble': 'wobble 1s 2 ',
      }
    },
  },
  plugins: [
      require('@tailwindcss/forms'),
  ],
}
