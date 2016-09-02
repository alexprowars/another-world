'use strict';

var gulp = require('gulp');
var sass = require('gulp-sass');
var autoprefixer = require('gulp-autoprefixer');
var uglifycss = require('gulp-uglifycss');

gulp.task('sass', function ()
{
	gulp.src('./public/css/src/game/**/*.scss')
		.pipe(sass().on('error', sass.logError))
		.pipe(autoprefixer())
		.pipe(uglifycss())
    	.pipe(gulp.dest('./public/css'));

	gulp.src('./app/modules/Core/Classes/Prophiler/View/sass/*.scss')
			.pipe(sass())
			.pipe(uglifycss())
			.pipe(gulp.dest('./app/modules/Core/Classes/Prophiler/View/css'));
});

gulp.task('watch', function ()
{
	gulp.watch('./public/css/src/game/**/*.scss', ['sass']);
	gulp.watch('./public/css/src/bootstrap/**/*.scss', ['bootstrap']);
});

gulp.task('bootstrap', function ()
{
	gulp.src('./public/css/src/bootstrap/**/*.scss')
		.pipe(sass().on('error', sass.logError))
		.pipe(autoprefixer())
		.pipe(uglifycss())
    	.pipe(gulp.dest('./public/css'));
});

gulp.task('default', ['sass', 'bootstrap']);