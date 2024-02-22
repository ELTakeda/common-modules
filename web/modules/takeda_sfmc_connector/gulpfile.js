'use strict';

const gulp = require('gulp');
const sass = require('gulp-sass')(require('sass'));

function build() {
    return gulp.src('./scss/**/*.scss') // The folder in which the SCSS is located
        .pipe(sass({outputStyle: 'compressed'}).on('error', sass.logError)) // Sets the output to be compressed
        .pipe(gulp.dest('./styles')); // The output folder destination
};

exports.build = build;
exports.watch = function () {
    gulp.watch('./scss/**/*.scss', build);
};