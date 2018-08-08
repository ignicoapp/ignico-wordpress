/**
 * Gulp task provider for processing styles
 */
import config from './config';

import gulp from 'gulp';
import clean from 'gulp-clean';
import rename from 'gulp-rename';
import sass from 'gulp-sass';
import postcss from 'gulp-postcss';
import sourcemaps from 'gulp-sourcemaps';
import iconfont from 'gulp-iconfont';
import iconfontCss from 'gulp-iconfont-css';

let watcher;

var postcssPlugins = [];

/**
 * Task provided for cleaning css directory
 */
gulp.task('clean:css', function() {

    return gulp.src(config.distCssPath + '/*', {
        read: false
    }).pipe(clean());

});

gulp.task('stylesheet:compile:development', function() {

    return gulp.src(config.srcScssPath + '/*.scss')
        // Init sourcemap
            .pipe(sourcemaps.init())
        // Compile scss files to css
            .pipe(sass(config.sassOptionsDevelopment).on('error', sass.logError))
        // Compile css files to css
            .pipe(postcss(postcssPlugins))
        // Save css file
            .pipe(gulp.dest(config.distCssPath));

});

gulp.task('stylesheet:compile:production', function() {

    return gulp.src(config.srcScssPath + '/*.scss')
        // Compile scss files to css
            .pipe(sass(config.sassOptionsProduction).on('error', sass.logError))
        // Compile css files to css
            .pipe(postcss(postcssPlugins))
		// Rename style.css to style.min.css
			.pipe(rename({
				suffix: '.min'
			}))
        // Save css file
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
 * Task provided for processing scss files for development purpose
 */
gulp.task('stylesheet:development', gulp.series([
	'stylesheet:iconfont',
    'stylesheet:compile:development',
	'stylesheet:compile:production'
]));

/**
 * Task provided for processing scss files for production purpose
 */
gulp.task('stylesheet:production', gulp.series([
	'stylesheet:iconfont',
	'stylesheet:compile:development',
    'stylesheet:compile:production'
]));

/**
 * Task provided for listening changes on files and processing it
 */
gulp.task('watch:stylesheet', function(done) {

    watcher = gulp.watch(config.cssWatchGlob, gulp.series('wdisable:stylesheet', 'stylesheet:development', 'wenable:stylesheet'));
    done();

});

gulp.task('wdisable:stylesheet', function(done) {

    watcher.unwatch(config.cssWatchGlob);
    done();

});

gulp.task('wenable:stylesheet', function(done) {

    watcher.add(config.cssWatchGlob);
    done();

});
