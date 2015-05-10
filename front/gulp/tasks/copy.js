var gulp = require('gulp');
var concat = require('gulp-concat');
var cssmin = require('gulp-cssmin');
var uglify = require('gulp-uglify');
var config = require('../config');

// Concat vendor css
gulp.task('copy:vendor_css', function() {
    var vendor_css = [
        config.source + '/bower/json-human/css/json.human.css'
    ];

    return gulp.src(vendor_css)
        .pipe(concat('vendors.css'))
        .pipe(cssmin())
        .pipe(gulp.dest(config.dest + '/css'));
});

// Concat vendor JS
gulp.task('copy:vendor_js', function() {
    var vendor_js = [
        config.source + '/bower/json-human/src/json.human.js'
    ];

    return gulp.src(vendor_js)
        .pipe(concat('vendors.js'))
        .pipe(uglify())
        .pipe(gulp.dest(config.dest));
});

// Copy scripts
gulp.task('copy:app_js', function() {
    var src_js = [
        config.source + '/javascript/config.js',
        config.source + '/javascript/app.js'
    ];

    return gulp.src(src_js)
        .pipe(concat('app.js'))
        .pipe(uglify())
        .pipe(gulp.dest(config.dest));
});

// Copy all
gulp.task('copy', ['copy:vendor_css', 'copy:vendor_js', 'copy:app_js']);
