DspSoftsCronManagerBundle
=========================

[![Latest Version](https://img.shields.io/github/release/dspsofts/cronmanager-bundle.svg?style=flat-square)](https://github.com/dspsofts/cronmanager-bundle/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/dspsofts/cronmanager-bundle/master.svg?style=flat-square)](https://travis-ci.org/dspsofts/cronmanager-bundle)
[![Coverage Status](https://img.shields.io/scrutinizer/coverage/g/dspsofts/cronmanager-bundle.svg?style=flat-square)](https://scrutinizer-ci.com/g/dspsofts/cronmanager-bundle/code-structure)
[![Quality Score](https://img.shields.io/scrutinizer/g/dspsofts/cronmanager-bundle.svg?style=flat-square)](https://scrutinizer-ci.com/g/dspsofts/cronmanager-bundle)

The DspSoftsCronManagerBundle provides a simple way to launch and control any cron job.


Installation
------------

This cron manager uses a main command which runs all crons. The command `dsp:cron:run` should be run every minute. 
In order to do this, you can put this line in your crontab:

    * * * * * /path/to/symfony/app/console dsp:cron:run


License
-------

This bundle is under the MIT license. See the complete license in the bundle:

    Resources/meta/LICENSE
