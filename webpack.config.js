const dotenv = require('dotenv').config({path: __dirname + '/.env'})
const isDevelopment = process.env.NODE_ENV !== 'production'
var webpack = require('webpack');

module.exports = {
    entry: {
        babel: "@babel/polyfill",
        react_wizard: './src/react_wizard.jsx'
    },
    output: {
        filename: '[name].js',
        path: __dirname + '/dist',
    },
    plugins: [
        new webpack.DefinePlugin({
            "process.env.NODE_ENV": JSON.stringify(process.env.NODE_ENV),
            "process.env.MY_ENV": JSON.stringify(process.env.MY_ENV),
        }),
        new webpack.ProvidePlugin({
            "process": 'process/browser',
        }),
    ],
    resolve: {
        fallback: {
            "http": require.resolve("stream-http"),
            "https": require.resolve("https-browserify"),
            "stream": require.resolve('stream-browserify'),
            "crypto": require.resolve('crypto-browserify')
        }
    },
    module: {
        rules: [
            {
                test: /\.(jsx)$/,
                exclude: /node_modules/,
                use: {
                    loader: 'babel-loader',
                    options: {
                        cacheDirectory: false,
                        cacheCompression: false,
                        presets: ['@babel/react', '@babel/env'],
                        plugins: ['@babel/proposal-class-properties']
                    }
                }
            }
        ]
    },
    cache: false,
};