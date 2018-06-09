/**
 * Config file with base variables.
 *
 * To load variables into file just include this file. e.g.
 *
 * var config = require('./config');
 * console.log(config.src);
 */
import gulp from 'gulp';
import notify from 'gulp-notify';

/**
 * Base variables
 *
 * Path relative to path where gulp is executed. Probably root directory
 */
let src = './assets';
let dist = '.';
let date = Date.now();

/**
 * File paths
 */
let srcScssPath = src + '/scss';
let distCssPath = dist + '/css';

let gulpPath = './tasks';

let srcSvgPath = src + '/svg';

let srcFontsPath = src + '/fonts';
let distFontsPath = dist + '/fonts';

let cssWatchGlob = [ srcScssPath + '/**/*.scss' ];

/**
 * Watch options
 */

let watchOptions = {
    interval: 100,
    debounceDelay: 5000
};

/**
 * Sass options
 */
let sassOptions = {};

let sassOptionsDevelopment = Object.assign({
    outputStyle: 'nested'
}, sassOptions);

let sassOptionsProduction = Object.assign({
    outputStyle: 'compressed'
}, sassOptions);

let config = {
    src,
    dist,
    date,
	srcScssPath,
    distCssPath,

    gulpPath,

    srcSvgPath,

    srcFontsPath,
    distFontsPath,

    cssWatchGlob,

    sassOptions,
    sassOptionsDevelopment,
    sassOptionsProduction,
};

export default config;
