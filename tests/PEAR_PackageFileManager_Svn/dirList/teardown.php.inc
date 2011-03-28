<?php
// Cleanup
$file = dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'temp' . DIRECTORY_SEPARATOR;

unlink($file . '.svn/entries');
unlink($file . 'test1.txt');
unlink($file . 'test2.txt');
unlink($file . '.test');

rmdir($file . '.svn');
rmdir($file);