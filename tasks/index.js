import gulp from 'gulp'

import stylesheet from './stylesheet';

gulp.task('watch', gulp.parallel( [
    'watch:stylesheet'
] ));

gulp.task('build:dev', gulp.series([
	'stylesheet:development'
]));

gulp.task('build:prod', gulp.series([
	'stylesheet:production'
]));

gulp.task('dev', gulp.series([
    'build:dev',
    'watch'
]));
