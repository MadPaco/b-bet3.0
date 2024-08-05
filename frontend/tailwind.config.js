/** @type {import('tailwindcss').Config} */
import plugin from 'tailwindcss/plugin';
import teamColorCodes from './teamColorCode';

export default {
  content: ['./index.html', './src/**/*.{js,ts,jsx,tsx}'],
  theme: {
    extend: {
      keyframes: {
        pulseGlow: {
          '50%': { boxShadow: '0 0 20px 10px rgba(224, 159, 31, 0.8)' },
          '0%, 100%': { boxShadow: '0 0 10px 3px rgba(224, 159, 31, 0.5)' },
        },

      },
      animation: {
        pulseGlow: 'pulseGlow 2s infinite',
        'ping-once': 'ping 1s cubic-bezier(0, 0, 0.2, 1)',
      },
      textShadow: {
        sm: '0 1px 2px var(--tw-shadow-color)',
        DEFAULT: '0 2px 4px var(--tw-shadow-color)',
        lg: '0 8px 16px var(--tw-shadow-color)',
      },
      backgroundImage: {
        'stadiumTop': "url('/assets/images/stadium2.webp')",
      },
      colors: {
        ...teamColorCodes,
        highlightBlue: '#259EA4',
        highlightGold: '#E09F1F',
        highlightLightBlue: '#DCF2FF',
        highlightGreen: '#75FAC7',
        highlightCream: '#FFF7D6',
      },
    },
  },
  plugins: [
    plugin(function ({ addUtilities }) {
      const newUtilities = {
        '.goldGlow': {
          boxShadow: '0 0 20px 5px rgba(224, 159, 31, 0.75)',
        },
      };
      addUtilities(newUtilities, ['responsive', 'hover', 'focus-within']);
    }),
    plugin(function ({ matchUtilities, theme }) {
      matchUtilities(
        {
          'text-shadow': (value) => ({
            textShadow: value,
          }),
        },
        { values: theme('textShadow') }
      );
    }),
  ],
};
