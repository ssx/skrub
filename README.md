# Skrub.

Skrub is a composer plugin that will remove useless files and free up some space, 
especially useful when trying to keep Docker images small. A lot of OSS project
owners use .gitattributes to strip junk out but the majority don't.


### Installation
Just run `composer require ssx/skrub` and you're good to go.


### Usage

`composer skrub` will list the files Skrub feels it can safely remove and the 
total disk space that can be regained.

Adding `--perform` to the command will actually delete the files &amp; 
directories from your system.

[![asciicast](https://asciinema.org/a/NHImku9pWOwM2EY34YZTq4RZu.svg)](https://asciinema.org/a/NHImku9pWOwM2EY34YZTq4RZu)

### Warranty, Disclaimer etc.

This is a plugin that will perform a deletion command on your system. The 
author is in no way liable for any data loss arising from its use or if it 
deletes unexpected files. Pay attention to the files Skrub lists.   


### Security Issues

If you discover a security issue with Skrub, please email scott@dor.ky and I'll
response as soon as possible.
