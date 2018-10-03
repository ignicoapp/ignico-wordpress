/**
 * Config file with base variables.
 *
 * To load variables into file just include this file. e.g.
 *
 * var config = require('./config');
 * console.log(config.src);
 */
import gulp from 'gulp';

/**
 * Base variables
 *
 * Path relative to path where gulp is executed. Probably root directory
 */
let src = '.';
let dist = '.';
let date = Date.now();

/**
 * File paths
 */
let srcAssets = src + '.assets';
let srcScssPath = srcAssets + '/scss';
let distCssPath = dist + '/css';

let gulpPath = './tasks';

let srcSvgPath = srcAssets + '/svg';

let srcFontsPath = srcAssets + '/fonts';
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
    srcAssets,
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
