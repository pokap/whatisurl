var gulp = require('gulp');
var sass = require('gulp-ruby-sass');
var prefix = require('gulp-autoprefixer');
var concat = require('gulp-concat');
var config = require('../config');

gulp.task('sass', function () {
    gulp.src(config.source + '/scss/*.scss')
        .pipe(sass({style: 'compressed'}))
        .pipe(prefix("last 10 version", "> 1%", "ie 8"))
        .pipe(concat('main.css'))
        .pipe(gulp.dest(config.dest + '/css'));
});
