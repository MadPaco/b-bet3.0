/** @type {import('tailwindcss').Config} */
import teamColorCodes from './teamColorCode';

export default {
  content: ['./index.html', './src/**/*.{js,ts,jsx,tsx}'],
  theme: {
    extend: {
      backgroundImage: {
        'stadiumTop': "url('/assets/images/stadium2.webp')",
      },
      animation: {
        'ping-once': 'ping 1s cubic-bezier(0, 0, 0.2, 1)',
      },
      colors: {
        ...teamColorCodes,
        highlightBlue: '#259EA4',
        highlightGold: '#E09F1F',
      },
    },
  },
  plugins: [],
};