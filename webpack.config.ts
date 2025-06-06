import * as path from 'path';
import DotenvWebpackPlugin from 'dotenv-webpack'; // Importar el plugin dotenv-webpack

export default {
  entry: './src/frontend/main.ts', // Entrada de la aplicación
  output: {
    filename: 'bundle.js', // Nombre del archivo de salida
    path: path.resolve(__dirname, 'dist'), // Directorio de salida
    publicPath: '/', // Ruta pública para acceder a los archivos
  },
  module: {
    rules: [
      {
        test: /\.ts$/, // Regla para compilar archivos .ts
        use: 'esbuild-loader', // Usar esbuild para la compilación
        exclude: /node_modules/, // Excluir node_modules
      },
      {
        test: /\.css$/, // Regla para compilar archivos .css
        use: ['style-loader', 'css-loader'], // Usar style-loader y css-loader
      },
    ],
  },
  resolve: {
    extensions: ['.ts', '.js'], // Resolver archivos .ts y .js
  },
  plugins: [
    // Usamos dotenv-webpack para cargar las variables de entorno desde .env
    new DotenvWebpackPlugin({
      path: './.env.prod', // Ruta a tu archivo de variables de entorno
    }),
  ],
  devServer: {
    static: path.join(__dirname, 'dist'), // Directorio de distribución para el servidor
    compress: true, // Habilitar la compresión
    port: 9000, // Puerto en el que se ejecutará el servidor
  },
  optimization: {
    minimize: true, // Habilitar la minimización del código
    usedExports: true, // Eliminar código no utilizado
  },
  devtool: false, // Deshabilitar la generación de source maps
};
