import gulp from "gulp";
import zip from "gulp-zip";

// Plugin details
const pluginName = "neoweb-auto-updater"; // Replace with your plugin folder name
const outputFileName = `${pluginName}.zip`;

// Paths
const pluginDir = "./"; // The root directory of your plugin
const destination = "./dist"; // Output folder for the ZIP file

// Task to create ZIP
gulp.task("zip", function () {
  return gulp
    .src([
      `${pluginDir}/**/*`, // Include all files and folders
      `!${pluginDir}/dist/**`, // Exclude 'dist' folder itself
      `!${pluginDir}/node_modules/**`, // Exclude node_modules folder
      `!${pluginDir}/.git/**`, // Exclude .git folder
      `!${pluginDir}/gulpfile.js`, // Exclude the gulpfile itself
      `!${pluginDir}/README.md`, // Exclude the gulpfile itself
      `!${pluginDir}/package*`, // Exclude package.json and package-lock.json
    ])
    .pipe(zip(outputFileName))
    .pipe(gulp.dest(destination));
});

// Default task
gulp.task("default", gulp.series("zip"));
