var gulp = require('gulp');
var sass = require('gulp-ruby-sass');

var paths = {
	css: 'skin/frontend/snowdog/default/css/**',
	sass: 'skin/frontend/snowdog/default/scss/**'
};

gulp.task('watch', function() {
	gulp.watch(paths.sass, ['sass']);
});

gulp.task('sass', function() {
	return sass('skin/frontend/snowdog/default/scss', { 
		sourcemap: false,
		trace: true,
		unixNewlines: true, // false on Windows, otherwise true
		check: true,
		style: 'expanded',
		quiet: false,
		lineNumbers: true
	})
	.on('error', function (err) { console.error('Error', err.message); })
	.pipe(gulp.dest('skin/frontend/snowdog/default/css'));
});