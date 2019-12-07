// Required plugins
var gulp         = require('gulp');
var plumber      = require('gulp-plumber');
var gulpif       = require('gulp-if');
var notify       = require("gulp-notify");
var gutil        = require('gulp-util');
var sass         = require('gulp-sass');
var sourcemaps   = require('gulp-sourcemaps');
var postcss      = require('gulp-postcss');
var csslint      = require('gulp-csslint');
var browserSync  = require('browser-sync');
var reload       = browserSync.reload;
var eslint       = require('gulp-eslint');
var logger       = require('gulp-logger');
var runSequence  = require('run-sequence');
var autoprefixer = require('autoprefixer');

// PostCSS Processors Config
var processors = [
    autoprefixer()
];

// Project Config
var config = {
    package:    'snowdog',
    theme:      'sockbin',
    esLint:     './.eslintrc',
    cssLint:    './.csslintrc',
    bsSettings: require('./local/bssettings.json'),
    maps:       false
};

// Project Paths
var paths = {
    template       : '../app/design/frontend/' + config.package + '/' + config.theme + '/**',
    skin           : '../skin/frontend/'       + config.package + '/' + config.theme,
    sass           : '../skin/frontend/'       + config.package + '/' + config.theme + '/scss/**',
    css            : '../skin/frontend/'       + config.package + '/' + config.theme + '/css',
    scripts        : '../skin/frontend/'       + config.package + '/' + config.theme + '/js/**'
};

// Custom Reporter
function customReporter(file) {
    gutil.log(gutil.colors.cyan(file.csslint.errorCount) + ' errors in ' + gutil.colors.magenta(file.path));

    file.csslint.results.forEach(function(result) {
        if (result.error.type === 'warning') {
            gutil.log( gutil.colors.yellow.bold('[Warining]') + gutil.colors.green(' Line: ' + result.error.line) + gutil.colors.cyan(' Column: ' + result.error.col) + ' ' + gutil.colors.magenta(result.error.message) + ' ' +  gutil.colors.gray(result.error.rule.desc) + ' ' + gutil.colors.red('Browsers: ' + result.error.rule.browsers));
        } else {
            gutil.log( gutil.colors.red.bold('[' + result.error.type + ']') + gutil.colors.green(' Line: ' + result.error.line) + gutil.colors.cyan(' Column: ' + result.error.col) + ' ' + gutil.colors.magenta(result.error.message) + ' ' +  gutil.colors.gray(result.error.rule.desc) + ' ' + gutil.colors.red('Browsers: ' + result.error.rule.browsers));
        }
    });
}

// -------------------------------------------
// Default
// -------------------------------------------

gulp.task('default', ['browsersync', 'watch', 'watch-css-and-lint', 'scripts']);

// -------------------------------------------
// Browser Sync
// -------------------------------------------

gulp.task('browsersync', function() {
    browserSync({
        proxy: config.bsSettings.proxy
    });
});

// -------------------------------------------
// Watch
// -------------------------------------------

gulp.task('watch', function() {
    gulp.watch(paths.sass + '/**', ['sass']);
    gulp.watch(paths.template, reload);
    gulp.watch(paths.scripts, reload);
});

// -------------------------------------------
// SASS
// -------------------------------------------

gulp.task('sass', function() {
    return gulp.src([paths.sass + '/*.scss'])
    .pipe(plumber({ errorHandler: notify.onError("Error: <%= error.message %>") }))
    .pipe(gulpif(config.maps, sourcemaps.init()))
    .pipe(sass({ outputStyle: 'expanded' }))
    .pipe(postcss(processors))
    .pipe(gulpif(config.maps, sourcemaps.write('./')))
    .pipe(gulp.dest(paths.css))
    .pipe(reload({ stream: true }));
});

// -------------------------------------------
// Styles
// -------------------------------------------

gulp.task('styles', function() {
    return gulp.src(paths.sass + '/**/.scss')
    .pipe(plumber({ errorHandler: notify.onError("Error: <%= error.message %>") }))
    .pipe(sass({ outputStyle: 'expanded' }))
    .pipe(postcss(processors))
    .pipe(gulp.dest(paths.css));
});

// -------------------------------------------
// Lint
// -------------------------------------------

gulp.task('eslint', function() {
    if (gutil.env.file) {
        gulp.watch('../**/' + gutil.env.file + '.js', function(event) {
            gulp.src(event.path)
            .pipe(plumber({ errorHandler: notify.onError("ESLint found problems") }))
            .pipe(logger({ display: 'name' }))
            .pipe(eslint(config.esLint))
            .pipe(eslint.format());
        });
    }
    else {
        gutil.log( gutil.colors.red.bold('ERROR: Specify file name, for example: ') + gutil.colors.green('gulp eslint --file formValidator-2.2.8'));
    }
});

// -------------------------------------------
// Scripts
// -------------------------------------------

gulp.task('scripts', function() {
    gulp.watch(paths.custom, function(event) {
        gulp.src(event.path)
        .pipe(plumber({ errorHandler: notify.onError("ESLint found problems") }))
        .pipe(logger({ display: 'name' }))
        .pipe(eslint(esLint))
        .pipe(eslint.format());
    });
});

// -------------------------------------------
// Watch Lint CSS
// -------------------------------------------

gulp.task('watch-css-and-lint', function() {
    gulp.watch([paths.css + '/*.css'], function(event) {
        gulp.src(event.path)
        .pipe(plumber({ errorHandler: notify.onError("Error: <%= error.message %>") }))
        .pipe(logger({ display: 'name'}))
        .pipe(csslint(config.cssLint))
        .pipe(csslint.reporter(customReporter));
    });
});

// -------------------------------------------
// Lint CSS
// -------------------------------------------

gulp.task('css-lint', function() {
    return gulp.src([paths.css + '/*.css'])
    .pipe(plumber({ errorHandler: notify.onError("Error: <%= error.message %>") }))
    .pipe(logger({ display: 'name'}))
    .pipe(gulpif(gutil.env.full, csslint(), csslint(config.cssLint)))
    .pipe(csslint.reporter(customReporter));
});

// -------------------------------------------
// Release
// -------------------------------------------

gulp.task('release', function() {
    runSequence('styles', 'css-lint');
});