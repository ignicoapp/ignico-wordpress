import gulp from 'gulp'
import clean from 'gulp-clean';

import config from './config'

import './stylesheet';
import './javascript';
import './bump';

/**
 * Remove .checksums file
 */
gulp.task('clean', function () {
    return gulp.src(config.src + '/../.checksums', { allowEmpty: true }).pipe(clean());

});

gulp.task('inspect', gulp.series([
    'javascript:inspect'
]));

gulp.task('build', gulp.series([
    'clean',
    'stylesheet:build',
    'javascript:build'
]));

gulp.task('watch', gulp.parallel( [
    'build',
    'stylesheet:watch',
    'javascript:watch'
] ));

gulp.task('default', gulp.series(['build']));
