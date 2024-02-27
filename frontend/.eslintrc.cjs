module.exports = {
    root: true,
    env: {
        browser: true,
        es2020: true,
        'cypress/globals': true, // Add this line
    },
    extends: [
        'eslint:recommended',
        'plugin:react/recommended',
        'plugin:react/jsx-runtime',
        'plugin:react-hooks/recommended',
        'plugin:cypress/recommended', // And this line
    ],
    ignorePatterns: ['dist', '.eslintrc.cjs'],
    parserOptions: { ecmaVersion: 'latest', sourceType: 'module' },
    settings: { react: { version: '18.2' } },
    plugins: ['react-refresh', 'cypress'], // And this line
    rules: {
        'react-refresh/only-export-components': ['warn', { allowConstantExport: true }],
        semi: ['warn', 'always'], // Enforces semicolons at the end of statements
        eqeqeq: ['error', 'always'], // Enforces the use of === and !==
        curly: 'error', // Enforces consistent brace style for all control statements
        'no-console': 'warn', // Warns on console.log usage
        'no-debugger': 'error', // Disallows the use of debugger
        'default-case': 'error', // Requires default case in switch statements
        'react-hooks/rules-of-hooks': 'error', // Checks rules of Hooks
        'react-hooks/exhaustive-deps': 'warn', // Checks effect dependencies
        'react/prop-types': 'off', // Turn off prop-types as we assume a TypeScript environment
    },
};
