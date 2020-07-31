/**
 * Gulp task provider for processing styles
 */
import minimist from 'minimist';

import gulp from 'gulp';
import bump from 'gulp-bump';

import config from './config';

let defaults = {
	string: 'type',
	default: {
		type: 'prerelease'
	}
};

let options = minimist(process.argv.slice(2), defaults);

/**
 * Task provided for prerelease bumping of the package manager files
 */
gulp.task('bump:packages', function() {

	return gulp.src([
		config.root + '/composer.json',
		config.root + '/package.json',
	])
		.pipe(bump({
			type: options.type
		}))
		.pipe(gulp.dest(config.dist));

});

/**
 * Task provided for prerelease bumping of the plugin file
 */
gulp.task('bump:plugin', function () {

	return gulp.src([
		config.root + '/ignico.php',
	])
		.pipe(bump({
			type: options.type
		}))
		.pipe(gulp.dest(config.dist));

});

/**
 * Task provided for prerelease bumping of the PHP constant in plugin file
 */
gulp.task('bump:constant', function () {

	let constant = 'IGNICO_VERSION';
	let regex = new RegExp(
		[
			// Match Key, e.g. "key": " OR 'key': ' OR 'key', ' OR "key", "" OR <key>
			'([<|\'|\"]?(',
			constant,
			')[>|\'|\"]?[ ]*[:=,]?[ |>]*[\'|\"]?[a-z]?)',

			// Match Semver version identifier, e.g.: x.y.z
			'(\\d+\\.\\d+\\.\\d+)',

			// Match Semver pre-release identifier, e.g. -pre.0-1
			'(-[0-9A-Za-z\.-]+)?',

			// Match Semver metadata identifier, e.g. +meta.0-1
			'(\\+[0-9A-Za-z\.-]+)?',

			// Match end of version value: e.g. ", ', <
			'([\'|\"|<]?)'
		].join(''), 'i'
	);

	return gulp.src([
		config.root + '/ignico.php',
	])
		.pipe(bump({
			type: options.type,
			regex: regex,
		}))
		.pipe(gulp.dest(config.dist));

});

/**
 * Task provided for bumping all plugin files.
 *
 * You can provide cli arguemnt `--type` to choose between major, minor, patch
 * and prerelease types.
 */
gulp.task('bump', gulp.series([
	'bump:packages',
	'bump:plugin',
	'bump:constant'
]));
