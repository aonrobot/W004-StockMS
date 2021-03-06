<?php

namespace Tests;

use Laravel\Dusk\TestCase as BaseTestCase;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;

abstract class DuskTestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * Prepare for Dusk test execution.
     *
     * @beforeClass
     * @return void
     */
    public static function prepare()
    {
        //static::startChromeDriver();
    }

    /**
     * Create the RemoteWebDriver instance.
     *
     * @return \Facebook\WebDriver\Remote\RemoteWebDriver
     */
    protected function driver()
    {
        if (env('APP_ENV') == 'travisci')
        {
            $options = (new ChromeOptions)->addArguments([
                '--disable-gpu',
                '--headless'
            ]);

            return RemoteWebDriver::create(
                'http://localhost:9222', DesiredCapabilities::chrome()
            );

        } else {
            return RemoteWebDriver::create(
                'http://localhost:4444/wd/hub', DesiredCapabilities::chrome()
            );
        }
    }
}
