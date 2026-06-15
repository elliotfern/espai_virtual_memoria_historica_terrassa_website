/* eslint-disable */
// @ts-nocheck

const path = require('path');
const webpack = require('webpack');
const dotenv = require('dotenv');

const isCI = process.env.CI === 'true';

let env = {};

if (!isCI) {
  // Carga .env.local, .env.production, .env.staging según NODE_ENV
  const nodeEnv = process.env.NODE_ENV || 'local';
  const result = dotenv.config({ path: `.env.${nodeEnv}` });
  env = result.parsed || {};
} else {
  env = process.env;
}

const envKeys = Object.keys(env).reduce((acc, key) => {
  acc[`process.env.${key}`] = JSON.stringify(env[key] || '');
  return acc;
}, {});

module.exports = {
  entry: '/src/frontend/main.ts',
  mode: process.env.NODE_ENV === 'production' ? 'production' : 'development',

  output: {
    path: path.resolve(__dirname, 'public/dist'),
    filename: 'bundle.js',
    publicPath: '/dist/',
    clean: true,
  },

  resolve: {
    extensions: ['.ts', '.js'],
  },

  module: {
    rules: [
      {
        test: /\.ts$/,
        exclude: /node_modules/,
        use: 'ts-loader',
      },
      {
        test: /\.css$/,
        use: ['style-loader', 'css-loader'],
      },
    ],
  },

  plugins: [
    new webpack.DefinePlugin(envKeys),
    new webpack.ProvidePlugin({
      process: 'process/browser',
    }),
  ],

  devtool: false,
};
