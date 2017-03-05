//*********** IMPORTS *****************
var gulp = require('gulp');
var autoprefixer = require('gulp-autoprefixer');
var sass = require('gulp-ruby-sass');
var gutil = require('gulp-util');
var rename = require("gulp-rename");
var map = require("map-stream");
var livereload = require("gulp-livereload");
var concat = require("gulp-concat");
var uglify = require('gulp-uglify');
var watch = require('gulp-watch');



gulp.task( 'watch', function () {
	gulp.watch( 'scss/*.scss', ['sass'] );
});
gulp.task( 'default', ['watch'] );


gulp.task( 'sass', function () {
	return sass( 'scss/*.scss', {style : 'expanded'} )
	.on( 'error', sass.logError )
	.pipe(autoprefixer())
	.pipe( gulp.dest( './css/' ) )
});
