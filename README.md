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

Step 1: Download the Bundle
~~~~~~~~~~~~~~~~~~~~~~~~~~~

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

.. code-block:: bash

    $ composer require dspsofts/cronmanager-bundle

This command requires you to have Composer installed globally, as explained
in the `installation chapter`_ of the Composer documentation.

Step 2: Enable the Bundle
~~~~~~~~~~~~~~~~~~~~~~~~~

Then, enable the bundle by adding the following line in the ``app/AppKernel.php``
file of your project:

.. code-block:: php

    // app/AppKernel.php

    class AppKernel extends Kernel
    {
        public function registerBundles()
        {
            $bundles = array(
                // ...

                new DspSofts\CronManagerBundle\DspSoftsCronManagerBundle(),
            );

            // ...
        }

        // ...
    }

Step 3: Configure the bundle
~~~~~~~~~~~~~~~~~~~~~~~~~~~~

You need to specify where to put the log files in your ``app/config/config.yml`` :

.. code-block:: yaml

    # app/config/config.yml
    dsp_softs_cron_manager:
        logs_dir: "%kernel.logs_dir%/cronmanager/%kernel.environment%"

.. 

Step 4: Add the runner command to your crontab
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

This cron manager uses a main command which runs all crons. The command `dsp:cron:run` should be run every minute. 
In order to do this, you can put this line in your crontab:

    * * * * * /path/to/symfony/app/console dsp:cron:run

Please be aware that the user which owns the crontab needs to have whatever permissions you need for your cron tasks.

License
-------

This bundle is under the MIT license. See the complete license in the bundle:

    Resources/meta/LICENSE
