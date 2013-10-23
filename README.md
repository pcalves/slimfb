slimfb
======

A starting point for creating Facebook applications and page tabs.

This repo combines awesome open-source libraries (Slim, Twig, Paris) with the Facebook PHP SDK, all loaded through Composer.

Disclaimer
--------------
This repository was created, first and foremost, to simplify my personal workflow. As such, it is very much a work-in-progress and sorely lacking in documentation. You are, of course, free and encouraged to fork it, try it, use it, abuse it, love it, hate it, help improve it, don't give a shit about it, and so on.

Installation
--------------
If you don't have [Composer](https://github.com/composer/composer) installed, *shame on you*. Otherwise:
    ```
    composer install
    ```

Configuration
------------------
Most configuring is done in the files in the **config** folder. One file per environment. These files are loaded at the top of **index.php**. The method for checking environments is all kinds of hacky and messy, but it does the job.

