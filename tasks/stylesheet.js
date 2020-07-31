/**
* Gulp task provider for processing styles
*/
import config from './config';

import gulp from 'gulp';
import once from 'gulp-once';
import clean from 'gulp-clean';
import sass from 'gulp-sass';
import postcss from 'gulp-postcss';
import rename from 'gulp-rename';
import sourcemaps from 'gulp-sourcemaps';

import stylelint from 'gulp-stylelint';

import iconfont from 'gulp-iconfont';
import iconfontCss from 'gulp-iconfont-css';

import scss from 'postcss-scss';
import sorting from 'postcss-sorting';

import autoprefixer from 'autoprefixer';

let postcssPlugins = [
  autoprefixer()
];

let developmentBuildTasks = ['stylesheet:fix', 'stylesheet:sort', 'stylesheet:lint', 'stylesheet:iconfont', 'stylesheet:scss'];
let productionBuildTasks  = ['stylesheet:lint', 'stylesheet:iconfont', 'stylesheet:scss', 'stylesheet:rename'];

let buildTasks = ( process.env.NODE_ENV === 'development' ) ? developmentBuildTasks : productionBuildTasks;

/**
* Task provided for cleaning css directory
*/
gulp.task('stylesheet:clean', function () {

  return gulp.src(config.distCssPath + '/*', {
    read: false
  }).pipe(clean());

});

gulp.task('stylesheet:lint', function () {

  return gulp.src(config.srcCssPath + '/**/*.scss')
    .pipe(once())
    // Sort scss files and save to source directory
    .pipe(stylelint({
      reporters: [
        {
          formatter: 'string',
          console: true
        }
      ],
      // Stylelint will break watch if this is not true
      failAfterError: false,
    }))
    // Save css file
    .pipe(gulp.dest(config.srcCssPath));

});

gulp.task('stylesheet:fix', function () {

  return gulp.src(config.srcCssPath + '/**/*.scss')
    .pipe(once())
    .pipe(stylelint({
      // Fix sccs files using styleling
      fix: true,

      // Stylelint will not save files if this is set to true
      failAfterError: false,
    }))
    // Save css file
    .pipe(gulp.dest(config.srcCssPath));

});

gulp.task('stylesheet:sort', function () {

  return gulp.src(config.srcCssPath + '/**/*.scss')
    .pipe(once())
    // Sort scss files and save to source directory
    .pipe(postcss([
      sorting()
    ], { parser: scss }))
    // Save css file
    .pipe(gulp.dest(config.srcCssPath));

});

gulp.task('stylesheet:scss', function () {

  return gulp.src(config.srcCssPath + '/*.scss')
    // Init sourcemap
    .pipe(sourcemaps.init())
    .pipe(sass(config.sassOptions).on('error', sass.logError))
	.pipe(postcss(postcssPlugins))
	.pipe(gulp.dest(config.distCssPath));

});

/**
* Task provided to add min suffix to output css files
*/
gulp.task('stylesheet:rename', function () {

  return gulp.src(config.distCssPath + '/*.css')
    .pipe(rename({
      suffix: ".min"
    }))
	.pipe(gulp.dest(config.distCssPath));

});

gulp.task('stylesheet:iconfont', function() {

	return gulp.src(config.srcSvgPath + '/icons/*.svg')
		.pipe(iconfontCss({
			fontName: 'ignico',
			path: 'scss',
			targetPath: '../assets/scss/tools/_icons.scss',
			fontPath: '../fonts/'
		}))
		.pipe(iconfont({
			fontName: 'ignico',
			prependUnicode: false,
			formats: ['ttf', 'eot', 'woff', 'woff2'],
			timestamp: Math.round( Date.now() / 1000 ), // recommended to get consistent builds when watching files
			fontHeight: 1000,
			normalize: true
		}))
		// Save font file
		.pipe(gulp.dest(config.distFontsPath));

});

/**
* Taks provided for inspect js files
*/
gulp.task('stylesheet:inspect', gulp.series(['stylesheet:lint']));

/**
* Taks provided for gather all build tasks
*/
gulp.task('stylesheet:build', gulp.series(buildTasks));

/**
* Task provided for listening changes on files and processing it
*/
gulp.task('stylesheet:watch', function () {
  return gulp.watch(config.cssWatchGlob, gulp.series(['stylesheet:build']));
});
