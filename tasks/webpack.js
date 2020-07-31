import path from 'path';
import merge from 'webpack-merge';

import MiniCssExtractPlugin  from 'mini-css-extract-plugin';
import UglifyJSPlugin from 'uglifyjs-webpack-plugin';

import config from './config';

let webpackConfig = {
    entry: {
        scripts: [
            config.srcJsPath + "/scripts.js",
        ],
    },
    output: {
        path: path.resolve(config.distJsPath),
        filename: '[name].js',
        chunkFilename: '[name].js',
    },
    module: {
        rules: [{
            test: /\.js$/,
            exclude: /node_modules/,
            loader: 'babel-loader'
        },
        {
            test: /\.css$/,
            use: [
                {
                    loader: MiniCssExtractPlugin.loader,
                    options: {
                        hmr: process.env.NODE_ENV === 'development',
                    }
                },
                'css-loader'
            ]
        }]
    },
    plugins: [
        new MiniCssExtractPlugin({
            filename: 'vendor.css',
            allChunks: true,
		})
    ]
}

switch(process.env.NODE_ENV) {
    case 'production':

        webpackConfig = merge(webpackConfig, {
            mode: 'production',
            plugins: [
                new UglifyJSPlugin()
            ]
        });

        break;

    case 'development':
    case 'local':
    default:

        webpackConfig = merge(webpackConfig, {
            mode: 'development'
        });

        break;
}

module.exports = webpackConfig;
