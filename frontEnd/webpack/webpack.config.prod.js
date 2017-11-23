let webpack = require("webpack");

let config = require('./webpack.config.base.js');

config.plugins.push(new webpack.DefinePlugin({
    'process.env': {
        NODE_ENV: '"production"'
    },
    __DEVELOPMENT__: false
}));
config.plugins.push(new webpack.optimize.UglifyJsPlugin({
    minimize: true,
    comments: false,
    compress: {
        warnings: false,
    }
}));
module.exports = config;
