<?php

namespace Bob\BuildConfig;

task('default', ['test', 'phpstan', 'sniff']);

desc('Run all tests');
task('test', ['phpspec', 'examples']);

desc('Run phpspec unit tests');
task('phpspec', ['descparser/src/Grammar.php'], function() {
    sh('phpspec run', null, ['failOnError' => true]);
    println('Phpspec unit tests passed');
});

desc('Tests documentation examples');
task('examples', ['descparser/src/Grammar.php'], function() {
    sh('readme-tester descparser/README.md receiptanalyzer/README.md matchmaker/README.md', null, ['failOnError' => true]);
    println('Documentation examples valid');
});

desc('Run statical analysis using phpstan');
task('phpstan', function() {
    sh('phpstan analyze -c phpstan.neon -l 7 descparser/src receiptanalyzer/src matchmaker/src', null, ['failOnError' => true]);
    println('Phpstan analysis passed');
});

desc('Check coding standard');
task('sniff', function() {
    sh('phpcs', null, ['failOnError' => true]);
    println('Coding standard analysis passed');
});

desc('Build description parser');
task('build_desc_parser', ['descparser/src/Grammar.php']);

$parserFiles = fileList('*.peg')->in([__DIR__ . '/descparser/src']);

fileTask('descparser/src/Grammar.php', $parserFiles, function() {
    sh('phpeg generate descparser/src/Grammar.peg', null, ['failOnError' => true]);
    println('Generated parser');
});

desc('Globally install development tools');
task('install_dev_tools', function() {
    sh('cgr scato/phpeg:^1.0', null, ['failOnError' => true]);
});
