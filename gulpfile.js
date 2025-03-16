import gulp from "gulp";
import zip from "gulp-zip";
import { deleteAsync } from "del"; // To handle cleaning up old files
import fs from "fs";

// Plugin details
const outerFolderName = "neoweb-auto-updater"; // Name of the folder in /build
const outputFileName = `${outerFolderName}.zip`; // Name of the ZIP file

// Paths
const pluginDir = "./"; // Root directory containing plugin files
const buildFolder = "./build"; // Folder where 'neoweb-auto-updater' will be created
const tempFolder = `${buildFolder}/${outerFolderName}`; // Path to the new folder
const destination = "./dist"; // Output directory for the ZIP file

// Task: Clean /build and /dist folders
gulp.task("clean", function () {
  return deleteAsync([buildFolder, destination]);
});

// Task: Create 'neoweb-auto-updater' folder and copy all files
gulp.task("prepare", function () {
  // Ensure the destination folders exist
  if (!fs.existsSync(buildFolder)) {
    fs.mkdirSync(buildFolder);
  }
  if (!fs.existsSync(destination)) {
    fs.mkdirSync(destination);
  }

  return gulp
    .src([
      `${pluginDir}/**/*`, // Include all plugin files
      `!${pluginDir}/build/**`, // Exclude build folder itself
      `!${pluginDir}/dist/**`, // Exclude dist folder
      `!${pluginDir}/node_modules/**`, // Exclude node_modules folder
      `!${pluginDir}/.git/**`, // Exclude .git folder
      `!${pluginDir}/gulpfile.js`, // Exclude gulpfile
      `!${pluginDir}/README.md`, // Exclude README file
      `!${pluginDir}/package*`, // Exclude package.json and package-lock.json
    ])
    .pipe(gulp.dest(tempFolder)); // Copy files into /build/neoweb-auto-updater
});

// Task: Zip the 'neoweb-auto-updater' folder
gulp.task("zip", function () {
  return gulp
    .src(`${tempFolder}/**/*`, { base: buildFolder }) // Include the outer folder
    .pipe(zip(outputFileName)) // Create the ZIP file
    .pipe(gulp.dest(destination)); // Save the ZIP in /dist
});

// Default task: Clean -> Prepare -> Zip
gulp.task("build", gulp.series("clean", "prepare", "zip"));

// Default task: Clean -> Prepare -> Zip
gulp.task("default", gulp.series("clean", "prepare", "zip"));
