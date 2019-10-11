/**
* Gulp task provider for processing javascript
*/
import config from './config';

import gulp from 'gulp';
import once from 'gulp-once';
import clean from 'gulp-clean';
import eslint from 'gulp-eslint';

import webpack from 'webpack';
import webpackStream from 'webpack-stream';

import webpackConfig from './webpack.js'

let developmentBuildTasks = ['javascript:fix', 'javascript:lint', 'javascript:compile'];
let productionBuildTasks  = ['javascript:lint', 'javascript:compile'];

let buildTasks = ( process.env.NODE_ENV === 'development' ) ? developmentBuildTasks : productionBuildTasks;

/**
 * Task provided for cleaning js directory
 */
gulp.task('javascript:clean', function() {

  return gulp.src(config.distJsPath + '/**/.js', {
      read: false
  }).pipe(clean());

});

/**
* Task provided for linting browser js files
*/
gulp.task('javascript:lint', function () {

  return gulp.src(config.srcJsPath + '/**/*.js', { buffer: true })
    .pipe(once())
    .pipe(eslint())
    .pipe(eslint.format())
    .pipe(gulp.dest(config.srcJsPath));
});

/**
* Task provided for fixing code styling in browser js files
*/
gulp.task('javascript:fix', function () {

  return gulp.src(config.srcJsPath + '/**/*.js', { buffer: true })
    .pipe(once())
    .pipe(eslint({ fix: true }))
    .pipe(gulp.dest(config.srcJsPath));
});

/**
* Task provided for "compile" browser js files by webpack
*/
gulp.task('javascript:compile', function () {

  return gulp.src(config.srcJsPath + '/**/*.js', { buffer: true })
		.pipe(webpackStream(webpackConfig, webpack))
		.pipe(gulp.dest(config.distJsPath));
});

/**
* Taks provided for gather all build tasks
*/
gulp.task('javascript:inspect', gulp.series(['javascript:lint']));

/**
* Taks provided for gather all build tasks
*/
gulp.task('javascript:build', gulp.series(buildTasks));

/**
* Task provided for listening changes on node js files and processing it
*/
gulp.task('javascript:watch', function () {
  return gulp.watch(config.jsWatchGlob, gulp.series(['javascript:build']));
});
