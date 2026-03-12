const purgecss = require('@fullhuman/postcss-purgecss')({
    content: [
        './public/**/*.php',
        './resources/js/**/*.js'
    ],
    defaultExtractor: content => content.match(/[\w-/:]+(?<!:)/g) || []
})

module.exports = {
    plugins: [
        ...(process.env.NODE_ENV === 'production' ? [purgecss] : [])
    ]
}