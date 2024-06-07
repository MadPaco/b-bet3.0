/** @type {import('tailwindcss').Config} */
import teamColorCodes from './teamColorCode';
export default {
  content: ['./index.html', './src/**/*.{js,ts,jsx,tsx}'],
  theme: {
    extend: {
      backgroundImage: () => ({
        grassfield: 'url(./src/assets/images/grassfield.jpg)',
        stadium: 'url(./src/assets/images/stadium.jpg)',
        stadiumTop: 'url(./src/assets/images/stadium2.jpg)',
      }),
      animation: {
        'ping-once': 'ping 1s cubic-bezier(0, 0, 0.2, 1)',
      },
      colors:teamColorCodes,
    },
  },
  plugins: [],
};
