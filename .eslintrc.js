module.exports = {
    env: {
        browser: true,
        commonjs: true,
        es6: true
    },
    extends: ['eslint:recommended', 'plugin:vue/recommended'],
    globals: {
        Atomics: 'readonly',
        SharedArrayBuffer: 'readonly'
    },
    parserOptions: {
        ecmaVersion: 2018
    },
    plugins: ['vue'],
    rules: {
        // 'no-console': process.env.NODE_ENV === 'production' ? 'error' : 'off',
        // 'no-debugger': process.env.NODE_ENV === 'production' ? 'error' : 'off',
        // singleQuote: true,
        // tabWidth: 4,

        // indent: ['error', 4, { 'SwitchCase': 1 }],
        "vue/max-attributes-per-line": ["error", {
            "singleline": 5,
            "multiline": {
                "max": 1,
                "allowFirstLine": false
            }
        }],
        "vue/html-indent": ["error", 4, {
            "attribute": 1,
            "baseIndent": 1,
            "closeBracket": 0,
            "alignAttributesVertically": true,
            "ignores": []
        }],
        "vue/script-indent": ["error", 4, { "baseIndent": 0 }],
        'linebreak-style': ['error', 'unix'],
        quotes: ['error', 'single'],
        semi: ['error', 'always']
    },
    overrides: [
        {
            files: ["*.vue"],
            rules: {
                "indent": "off"
            }
        }
    ]
};
