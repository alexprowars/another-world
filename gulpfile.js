'use strict';

var gulp = require('gulp');
var sass = require('gulp-sass');
var autoprefixer = require('gulp-autoprefixer');
var uglifycss = require('gulp-uglifycss');
var rename = require("gulp-rename");

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

gulp.task('metronic', function ()
{
	gulp.src('./public/assets/src/metronic/bootstrap.scss').pipe(sass()).pipe(gulp.dest('./public/assets/metronic/global/plugins/bootstrap/css/'));
	gulp.src('./public/assets/src/metronic/global/*.scss').pipe(sass()).pipe(gulp.dest('./public/assets/metronic/global/css'));
	gulp.src('./public/assets/src/metronic/pages/*.scss').pipe(sass()).pipe(gulp.dest('./public/assets/metronic/pages/css'));
	gulp.src('./public/assets/src/metronic/layout/*/*.scss').pipe(sass()).pipe(gulp.dest('./public/assets/metronic/pages/css'));
	gulp.src('./public/assets/src/metronic/layout/*/themes/*.scss').pipe(sass()).pipe(gulp.dest('./public/assets/metronic/pages/css'));

	gulp.src(['./public/assets/metronic/global/css/*.css', '!./public/assets/metronic/global/css/*.min.css']).pipe(uglifycss()).pipe(rename({suffix: '.min'})).pipe(gulp.dest('./public/assets/metronic/global/css'));
	gulp.src(['./public/assets/metronic/pages/css/*.css', '!./public/assets/metronic/pages/css/*.min.css']).pipe(uglifycss()).pipe(rename({suffix: '.min'})).pipe(gulp.dest('./public/assets/metronic/pages/css'));
	gulp.src(['./public/assets/metronic/pages/css/*/*.css', '!./public/assets/metronic/pages/css/*/*.min.css']).pipe(uglifycss()).pipe(rename({suffix: '.min'})).pipe(gulp.dest('./public/assets/metronic/pages/css'));

	gulp.src(['./public/assets/metronic/global/plugins/bootstrap/css/*.css', '!./public/assets/metronic/global/plugins/bootstrap/css/*.min.css']).pipe(uglifycss()).pipe(rename({suffix: '.min'})).pipe(gulp.dest('./public/assets/metronic/global/plugins/bootstrap/css'));

	gulp.src(['./public/assets/metronic/global/js/*.js', '!./public/assets/metronic/global/js/*.min.js']).pipe(uglifycss()).pipe(rename({suffix: '.min'})).pipe(gulp.dest('./public/assets/metronic/global/js'));
	gulp.src(['./public/assets/metronic/pages/js/*.js', '!./public/assets/metronic/pages/js/*.min.js']).pipe(uglifycss()).pipe(rename({suffix: '.min'})).pipe(gulp.dest('./public/assets/metronic/pages/js'));
	gulp.src(['./public/assets/metronic/layout/js/*.js', '!./public/assets/metronic/layout/js/*.min.js']).pipe(uglifycss()).pipe(rename({suffix: '.min'})).pipe(gulp.dest('./public/assets/metronic/layout/js'));
});

gulp.task('default', ['sass', 'bootstrap', 'metronic']);