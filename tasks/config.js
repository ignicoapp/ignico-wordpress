/**
 * Config file with base variables.
 *
 * To load variables into file just include this file. e.g.
 *
 * var config = require('./config');
 * console.log(config.src);
 */

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
let gulpPath = './tasks';

let srcCssPath = src + '/scss';
let distCssPath = dist + '/css';

let srcJsPath = src + '/js';
let distJsPath = dist + '/js';

let srcSvgPath = src + '/svg';

let srcFontsPath = src + '/fonts';
let distFontsPath = dist + '/fonts';

let cssWatchGlob = [ srcCssPath + '/**/*.scss' ];
let jsWatchGlob = [ srcJsPath + '/**/*.scss' ];

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

    gulpPath,

    srcCssPath,
	distCssPath,

	srcJsPath,
    distJsPath,

    srcSvgPath,

    srcFontsPath,
    distFontsPath,

    cssWatchGlob,
    jsWatchGlob,

    sassOptions,
    sassOptionsDevelopment,
    sassOptionsProduction,
};

export default config;
