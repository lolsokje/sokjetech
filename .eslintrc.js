module.exports = {
    'env': {
        'browser': true,
        'node': true,
        'es2021': true,
    },
    'extends': [
        'eslint:recommended',
        'plugin:vue/vue3-recommended',
    ],
    'parserOptions': {
        'ecmaVersion': 13,
        'sourceType': 'module',
    },
    'plugins': [
        'vue',
    ],
    'rules': {
        'indent': [
            'error',
            'tab',
        ],
        'linebreak-style': [
            'error',
            'unix',
        ],
        'quotes': [
            'error',
            'single',
        ],
        'semi': [
            'error',
            'always',
        ],
        'vue/multi-word-component-names': 'off',
        'vue/html-indent': ['error', 'tab'],
    },
};
