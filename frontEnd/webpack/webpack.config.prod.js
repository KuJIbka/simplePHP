let webpack = require("webpack");
let UglifyJSPlugin = require('uglifyjs-webpack-plugin');
let config = require('./webpack.config.base.js');

config.plugins.push(new webpack.DefinePlugin({
    'process.env': {
        NODE_ENV: '"production"'
    },
    __DEVELOPMENT__: false
}));
config.plugins.push(new UglifyJSPlugin({
    sourceMap: true,
    parallel: true,
    uglifyOptions: {
        ecma: 8,
        warnings: false
    },
}));
module.exports = config;
