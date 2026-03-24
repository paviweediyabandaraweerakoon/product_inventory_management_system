/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./app/Views/**/*.php",      // In all files within the Views directory
    "./public/**/*.php",       // In all files within the public directory (if you have any PHP files there)
    "./routes/**/*.php",       // In all files within the routes directory
    "./app/Controllers/**/*.php" // In all files within the Controllers directory
  ],
  theme: {
    extend: {},
  },
  plugins: [],
}