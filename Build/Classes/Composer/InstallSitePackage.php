<?php
namespace Neos\BaseDistribution\Composer;

use Composer\Console\Application;
use Composer\DependencyResolver\Operation\InstallOperation;
use Composer\DependencyResolver\Operation\UpdateOperation;
use Composer\Composer;
use Composer\Factory;
use Composer\Json\JsonFile;
use Composer\Package\Version\VersionParser;
use Composer\Repository\CompositeRepository;
use Composer\Repository\PlatformRepository;
use Composer\Script\Event;
use Composer\Installer\PackageEvent;
use Composer\Util\Silencer;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Yaml\Yaml;
use Neos\Utility\Files;
use Neos\Utility\Arrays;
use Neos\Splash\DistributionBuilder\Service\PackageService;
use Neos\Splash\DistributionBuilder\Service\JsonFileService;
use Neos\Splash\DistributionBuilder\Domain\ValueObjects\PackageRequirement;

/**
 *
 */
class InstallSitePackage
{
    const LOCAL_SRC_PATH = 'DistributionPackages';

    /**
     * Setup the neos distribution
     *
     * @param Event $event
     * @throws \Neos\Utility\Exception\FilesException
     */
    public static function setupDistribution(Event $event)
    {
        if (!defined('FLOW_PATH_ROOT')) {
            define('FLOW_PATH_ROOT', Files::getUnixStylePath(getcwd()) . '/');
        }
        $composer = $event->getComposer();
        $io = $event->getIO();

        $io->write([
            'Welcome to Neos',
            '',
            'Please answer some questions for finishing the setup of your Neos-distribution.',
            ''
        ]);


        $choices = [
            '' => 'None',
            'neos/demo' => 'Neos Demo',
            'dl/onepage' => 'DL Onepage'
        ];

        $selection = $io->select('Please select the site-package', $choices, '');

        if ($selection === '') {
            $io->write('No package will be installed.');
            return;
        }

        $output = new ConsoleOutput();
        $composerApplication = new Application();
        $composerApplication->doRun(new ArrayInput([
            'command' => 'require',
            'packages' => $selection
        ]), $output);

        // success
        $output->outputLine();
        $output->outputLine('Your distribution was prepared successfully.');
        $output->outputLine();
        $output->outputLine('For local development you still have to:');
        $output->outputLine();
        $output->outputLine('1. Add database credentials to Configuration/Development/Settings.yaml');
        $output->outputLine('2. Migrate database "./flow doctrine:migrate"');
        $output->outputLine('3. Import site data "./flow site:import --package-key ' . $sitePackageKey . ' "');
        $output->outputLine('4. Start the Webserver "./flow server:run"');
    }

}
