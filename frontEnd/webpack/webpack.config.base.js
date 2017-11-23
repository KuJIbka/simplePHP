let ExtractTextPlugin = require("extract-text-webpack-plugin");
let webpack = require("webpack");
let autoprefixer = require("autoprefixer");
let path = require("path");
let CopyWebpackPlugin = require("copy-webpack-plugin");

console.log(__dirname);

module.exports = {
    resolve: {
        modules: [path.join(__dirname, "/../src"), "node_modules"]
    },
    entry: {
        app: './src/index.js',
        appStyle: './sass/index.scss'
    },
    module: {
        rules: [
            {
                test: /\.js$/,
                exclude: [/node_modules/],
                use: {
                    loader: "babel-loader",
                    options: {
                        presets: ["react", "es2015", "stage-0"]
                    }
                }
            },
            {
                test: /fonts(\/|\\).+?\.(woff2?|ttf|otf|eot|svg)$/,
                use: {
                    loader: "url-loader?limit=10000&name=fonts/[name]-[hash:6].[ext]"
                }
            },
            {
                test: /jquery-ui(\/|\\).+?\.(svg|png|jpe?g|gif)$/,
                use: { loader: "url-loader?limit=10000&name=img/[name]-[hash:6].[ext]" }
            },
            {
                test: /img(\/|\\).+?\.(svg|png|jpe?g|gif)$/,
                use: { loader: "url-loader?limit=10000&name=img/[name]-[hash:6].[ext]" }
            },
            {
                test: /\.scss$/,
                use: ExtractTextPlugin.extract({
                    use: [
                        {
                            loader: "css-loader"
                        },
                        {
                            loader: "postcss-loader",
                            options: {
                                plugins: function () {
                                    return [autoprefixer('last 2 versions')]
                                }
                            }
                        },
                        {
                            loader: "sass-loader",
                            options: {
                                plugins: () => [require('autoprefixer')]
                            }
                        },
                    ],
                    fallback: "style-loader"
                })
            },
            {
                test: /.\css$/,
                use: ExtractTextPlugin.extract({
                    fallback: "style-loader",
                    use: "css-loader"
                })
            }
        ]
    },
    plugins: [
        // new webpack.ProvidePlugin({
        //     $: "jquery",
        //     jQuery: "jquery",
        //     "window.jQuery": "jquery",
        //     "window.$": "jquery"
        // }),
        //new webpack.optimize.UglifyJsPlugin({minimize: true}),
        // new webpack.LoaderOptionsPlugin({
        //     options: {
        //         postcss: [
        //             autoprefixer({browsers: ['last 2 versions']})
        //         ]
        //     }
        // }),
        new ExtractTextPlugin({
            filename: "./css/[name].css",
            allChunks: true
        }),
        new webpack.optimize.CommonsChunkPlugin({
            name: "common",
            minChunks: function(module, count) {
                if (module.resource && /analytics/.test(module.resource)) {
                    return false;
                }
                return count > 1;
            }
        }),
        new CopyWebpackPlugin([
//            { from: "./img/payment_systems_icons", to: "./img/payment_systems_icons" }
            // { from: './pic', to: './pic' }
        ])
    ],
    output: {
        path: path.resolve(__dirname, '../../web/static/'),
        publicPath: "/static/",
        filename: "./js/[name].js"
    }
};
