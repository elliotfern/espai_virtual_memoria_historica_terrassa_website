/* eslint-disable */
// @ts-nocheck

const path = require('path');
const webpack = require('webpack');
const dotenv = require('dotenv');

const nodeEnv = process.env.NODE_ENV || 'local';

/**
 * 🔥 SOLO usamos dotenv en local / desarrollo
 * En CI o producción confiamos en process.env del sistema
 */

const envFiles = {
  local: '.env.local',
  dev: '.env.dev',
  test: '.env.test',
  production: '.env.prod',
};

let env = {};

const envFile = envFiles[nodeEnv];

if (envFile) {
  const result = dotenv.config({
    path: envFile,
  });

  if (result.error) {
    throw new Error(`❌ Error loading ${envFile}`);
  }

  env = result.parsed || {};
} else {
  env = process.env;
}

/**
 * 🔥 SOLO variables permitidas
 */
const allowedKeys = ['API_BASE_URL', 'APP_ENV', 'DOMAIN_IMG', 'DOMAIN_WEB'];

const envKeys = allowedKeys.reduce((acc, key) => {
  const value = env[key];

  if (value === undefined || value === null || value === '') {
    throw new Error(`❌ Missing required env var: ${key}`);
  }

  acc[`process.env.${key}`] = JSON.stringify(value);
  return acc;
}, {});

module.exports = {
  entry: '/src/frontend/main.ts',

  mode: nodeEnv === 'production' ? 'production' : 'development',

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
        use: {
          loader: 'swc-loader',
          options: {
            jsc: {
              parser: {
                syntax: 'typescript',
              },
              target: 'es2021',
            },
            module: {
              type: 'es6',
            },
          },
        },
      },
      {
        test: /\.css$/,
        use: ['style-loader', 'css-loader'],
      },
    ],
  },

  plugins: [
    /**
     * 🔥 Inyección segura de env vars
     */
    new webpack.DefinePlugin(envKeys),

    /**
     * compatibilidad con process en frontend
     */
    new webpack.ProvidePlugin({
      process: 'process/browser',
    }),
  ],

  devtool: false,
};
