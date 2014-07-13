module.exports = (function(grunt) {
    'use strict';

    grunt.initConfig({
        'ftp-deploy': {
          build: {
            auth: {
              host: 'tangenttopic.com',
              port: 21,
              authKey: 'key1'
            },
            src: '/Users/danielburke/Documents/Development/DE/Sites/Forrent/FR-Tablet-Proto-API/',
            dest: 'domains/tangenttopic.com',
            exclusions: [
            '/Users/danielburke/Documents/Development/DE/Sites/Forrent/FR-Tablet-Proto-API/**/.DS_Store',
            '/Users/danielburke/Documents/Development/DE/Sites/Forrent/FR-Tablet-Proto-API/**/.ftppass',
            '/Users/danielburke/Documents/Development/DE/Sites/Forrent/FR-Tablet-Proto-API/**/Gruntfile.js',
            '/Users/danielburke/Documents/Development/DE/Sites/Forrent/FR-Tablet-Proto-API/**/package.json',
            '/Users/danielburke/Documents/Development/DE/Sites/Forrent/FR-Tablet-Proto-API/node_modules',
            '/Users/danielburke/Documents/Development/DE/Sites/Forrent/FR-Tablet-Proto-API/site',
            '/Users/danielburke/Documents/Development/DE/Sites/Forrent/FR-Tablet-Proto-API/.git']
          }
        }
    });

    grunt.loadNpmTasks('grunt-ftp-deploy');

    grunt.registerTask('default', [
        'ftp-deploy'
    ]);
});