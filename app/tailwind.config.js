
/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./Views/**/*.php",
    "./Controllers/**/*.php",
    "./resources/**/*.css",
    "./public/**/*.php"
  ],
  theme: {
    extend: {
      colors: {
        primary: "#EF233C",
        cream: "#F9FCE7",
        dark: "#000000"
      }
    }
  },
  plugins: []
};
