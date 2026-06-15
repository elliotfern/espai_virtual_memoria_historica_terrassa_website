export default {
    env: {
      node: true,
      es2020: true,
    },
    extends: [
      'eslint:recommended',
      'plugin:@typescript-eslint/recommended',
    ],
    parser: '@typescript-eslint/parser',
    parserOptions: {
      ecmaVersion: 2020,
      sourceType: 'module',
    },
    plugins: ['@typescript-eslint'],
    rules: {},

    // Excepción para ficheros de configuración Node.js/CommonJS
    overrides: [
      {
        files: ['webpack.config.js', '*.config.js'],
        env: { node: true },
        parserOptions: {
          sourceType: 'commonjs', // CommonJS para estos ficheros
        },
        rules: {
          '@typescript-eslint/no-require-imports': 'off',
          'no-undef': 'off',
        },
      },
    ],
  };