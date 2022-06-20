const path = require('path');
const glob = require('glob');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const TerserPlugin = require('terser-webpack-plugin');

module.exports = (env, options) => {
  const devMode = options.mode !== 'production';


  return {
    optimization: {
      minimize: true,
      minimizer: [new TerserPlugin()],
    },
    entry: {
      'app': glob.sync('./vendor/**/*.js').concat(['./js/app.js'])
    },
    output: {
      // clean: true,
      filename: '[name].js',
      chunkFilename: "[id].[contenthash].js",
      path: path.resolve(__dirname, '../public/fe_assets'),
      publicPath: '/fe_assets/'
    },
    devtool: devMode ? 'eval-cheap-module-source-map' : undefined,
    module: {
      rules: [{
          test: /\.(js|jsx)$/,
          exclude: /node_modules/,
          use: {
            loader: 'babel-loader'
          }
        },
        {
          test: /\.[s]?css$/,
          use: [
            MiniCssExtractPlugin.loader,
            'css-loader',
            // 'postcss-loader',
            // 'sass-loader'
          ],
        }
      ]
    },
    plugins: [
      new MiniCssExtractPlugin({
        filename: devMode ? '../public/fe_assets/app_js_dev.css' : '../public/fe_assets/app_js.css'
      })

    ]
  }
};
