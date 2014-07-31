module.exports = function(grunt) {
 
    // Configuration 
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'), 
        banner: [
	        '/*! \n* <%= pkg.name %> - v<%= pkg.version %>\n' ,
	        '* \n' ,
	        '* <%= grunt.template.today("dd-mm-yyyy h:m:s") %>\n' ,
	        '* <%= pkg.homepage %>/\n' ,
	        '* <%= pkg.author.name %> - Copyright (c) <%= grunt.template.today("yyyy") %> \n' ,
	        '* \n' ,
	        '* \n' ,
	        '\n* Licensed MIT  \n*/'
        ].join(""),
        concat: {
           css:{
           	src: 'dist/css/*.css',
           	dest: 'dist/_build/css/style.css'
           },
           js:{
           	src: ['dist/js/*.js','dist/js/plugins/*.js'],
           	dest: 'dist/_build/js/app.js'
           },
       	},
		uglify: {
			options: {
				mangle: true,
				compress: {
					drop_console: false
				},
				banner: '<%= banner %>',
			},
			js_front: {
				files: {
					'assets/js/custom-assets.min.js': ['dist/_build/js/app.js']
				}
			}
		},
		cssmin: {
		
			options: {
				banner: '<%= banner %>'
			},
			files: {
				dest: 'assets/css/custom-assets.min.css', 
				src: ['dist/_build/css/style.css']
			}
			
		},
		watch: {
		    js: {
		      files: ['dist/js/*.js','dist/js/plugins/*.js'],
		      tasks: ['js'],
		    },
			css: {
		      files: ['dist/css/*.css'],
		      tasks: ['css'],
			}
		}
    });
 
	// Plugins
	grunt.loadNpmTasks('grunt-contrib-concat');
	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-contrib-watch');
	grunt.loadNpmTasks('grunt-contrib-cssmin');
 
	// Tasks
	grunt.registerTask('default', ['concat', 'uglify', 'cssmin']);
	grunt.registerTask('js', ['concat:js', 'uglify']);
	grunt.registerTask('css', ['concat:css', 'cssmin']);
};