/** @type {import('tailwindcss').Config} */
export default {
  content: ['./index.html', './src/**/*.{js,ts,jsx,tsx}'],
  theme: {
    extend: {
      backgroundImage: () => ({
        grassfield: 'url(./src/assets/images/grassfield.jpg)',
        stadium: 'url(./src/assets/images/stadium.jpg)',
        stadiumTop: 'url(./src/assets/images/stadium2.jpg)',
      }),
    },
  },
  plugins: [],
};
