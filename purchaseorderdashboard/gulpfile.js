var gulp = require('gulp');
var concat = require('gulp-concat');
var rename = require('gulp-rename');
var uglify = require('gulp-uglify');

gulp.task('minify', done => {
    gulp.src('./js/custom.js') // path to your files
    .pipe(uglify())
    .pipe(rename('custom.min.js'))
    .pipe(gulp.dest('./js/'));
    done();
});
gulp.task('concat', done => {
    gulp.src('./js/*.min.js')
    .pipe(concat('combined.min.js'))
    .pipe(gulp.dest('js/build'));
    done();
});
gulp.task('default', gulp.series('minify', 'concat'));