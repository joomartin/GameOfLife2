var gulp = require('gulp');
var phpspec = require('gulp-phpspec');
var phpunit = require('gulp-phpunit');
var run = require('gulp-run');
var notify = require('gulp-notify');

gulp.task('spectest', function () {
    var errorNotify = {
        title: 'Epic fail',
        message: 'Your tests failed, Martin',
        icon: __dirname + '/fail.png'
    };

    var successNotify = {
        title: 'Green',
        message: 'All tests have returned green',
        icon: __dirname + '/success.png'
    };

   gulp.src('spec/**/*.php')
       .pipe(run('clear'))
       .pipe(phpspec('', { notify: true }))
       .on('error', notify.onError(errorNotify))
       .pipe(notify(successNotify));
});

gulp.task('unittest', function () {
    gulp.src('tests/**/*.php')
        .pipe(run('clear'))
        .pipe(phpunit())
        .on('error', function (e) {
            console.log('fail', e)
        });
});

gulp.task('watch', function () {
    gulp.watch(['spec/**/*.php'], ['spectest']);
    gulp.watch(['tests/**/*.php'], ['unittest']);
    gulp.watch(['src/**/*.php'], ['spectest', 'unittest']);
});

gulp.task('default', ['spectest', 'unittest', 'watch']);