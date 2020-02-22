const gulp = require('gulp');
const gulpSass = require('gulp-sass');
const gulpAutoPreFixer = require('gulp-autoprefixer');
const gulpPostCSS = require('gulp-postcss');
const cssMqpacker = require('css-mqpacker');
const paths = {
  scss: {
    src: './src/scss/**/*.scss',
    dest: './public/assets/css'
  }
};

gulp.task('scss', () => {
  return gulp
    .src(paths.scss.src)
    .pipe(
      gulpSass({
        outputStyle: 'compressed'
      })
    )
    .pipe(gulpAutoPreFixer())
    .pipe(
      gulpPostCSS([
        cssMqpacker({
          sort: function(a, b) {
            return a.localeCompare(b);
          }
        })
      ])
    )
    .pipe(gulp.dest(paths.scss.dest));
});

gulp.task('default', () => {
  gulp.watch(paths.scss.src, gulp.series('scss'));
});
