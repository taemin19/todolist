var Encore = require('@symfony/webpack-encore');

Encore
    // the project directory where compiled assets will be stored
    .setOutputPath('web/build/')
    // the public path used by the web server to access the previous directory
    .setPublicPath('/build')

    // empty the outputPath dir before each build
    .cleanupOutputBeforeBuild()

    .enableSourceMaps(!Encore.isProduction())

    // uncomment to create hashed filenames (e.g. app.abc123.css)
    // .enableVersioning(Encore.isProduction())

    // define the assets of the project
    .addEntry('js/app', './assets/js/app.js')
    .addStyleEntry('css/app', './assets/css/app.scss')

    // allow sass/scss files to be processed
    .enableSassLoader(function(sassOptions) {}, {
        resolveUrlLoader: false
    })

    // allow legacy applications that require $/jQuery as a global variable
    .autoProvidejQuery()

    // allow popper.js
    .autoProvideVariables({
        Popper: ['popper.js', 'default']
    })

;

module.exports = Encore.getWebpackConfig();
