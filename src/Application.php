<?php

namespace SilverStripe\Cow;

use SilverStripe\Cow\Commands;
use SilverStripe\Cow\Utility\SupportedModuleLoader;
use SilverStripe\Cow\Utility\Config;
use SilverStripe\Cow\Utility\GitHubApi;
use Symfony\Component\Console;

class Application extends Console\Application
{
    /**
     * Get version of this module
     *
     * @param string $directory
     * @return string
     */
    protected function getVersionInDir($directory)
    {
        if (!$directory || dirname($directory) === $directory) {
            return null;
        }
        $installed = $directory . '/vendor/composer/installed.json';
        if (file_exists($installed)) {
            $content = Config::loadFromFile($installed);
            foreach ($content as $library) {
                if ($library['name'] == 'silverstripe/cow') {
                    return $library['version'];
                }
            }
        } else {
            return $this->getVersionInDir(dirname($directory));
        }
    }

    /**
     * Get the name of the application used to run the command, eg: cow or bin/cow
     *
     * @return string
     */
    public function getBinName()
    {
        return isset($_SERVER['argv'][0]) ? $_SERVER['argv'][0] : 'cow';
    }

    public function getLongVersion()
    {
        $version = $this->getVersionInDir(__DIR__);
        return "<comment>cow release tool</comment> <info>{$version}</info>";
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultCommands()
    {
        $commands = parent::getDefaultCommands();

        // Create dependencies
        $githubApi = new GitHubApi();
        $supportedModuleLoader = new SupportedModuleLoader();

        // What is this cow doing in here, stop it, get out
        $commands[] = new Commands\MooCommand();

        // Release sub-commands
        $commands[] = new Commands\Release\Create();
        $commands[] = new Commands\Release\Plan();
        $commands[] = new Commands\Release\Branch();
        $commands[] = new Commands\Release\Translate();
        $commands[] = new Commands\Release\Test();
        $commands[] = new Commands\Release\Changelog();

        // Publish sub-commands
        $commands[] = new Commands\Release\Tag();

        // Base release commands
        $commands[] = new Commands\Release\Release();
        $commands[] = new Commands\Release\Publish();

        // Module commands
        $commands[] = new Commands\Module\TranslateBuild();
        $commands[] = new Commands\Module\Sync\Metadata($supportedModuleLoader);

        // Schema commands
        $commands[] = new Commands\Schema\Validate();

        // GitHub commands
        $commands[] = new Commands\GitHub\RateLimit($githubApi);
        $commands[] = new Commands\GitHub\SyncLabels($supportedModuleLoader, $githubApi);

        return $commands;
    }
}
