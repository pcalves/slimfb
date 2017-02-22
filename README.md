slimfb
======

A starting point for creating Facebook applications and page tabs.

This repo combines awesome open-source libraries (Slim, Twig, Paris) with the Facebook PHP SDK, all loaded through Composer.

Disclaimer
--------------
**🔥 This repo is not actively maintained and I have not kept up with changes to the Facebook API or to any of the code's required dependencies. Use at your own risk and be prepared for things to fail in fun and unexpected ways. 🔥**

Installation
--------------

```
composer install
```

Configuration
------------------
Most configuring is done in the files in the `config` folder. One file per environment. These files are loaded at the top of `index.php`. The method for checking environments is all kinds of hacky and messy, but it does the job.

