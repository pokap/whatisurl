var gulp = require('gulp');
var config = require('../config');

var watchjs = [
    config.source + '/javascript/**'
];

gulp.task('watch', function () {
    gulp.watch(config.source + '/scss/**', ['sass']);
    gulp.watch(watchjs, ['copy:app_js']);
});
