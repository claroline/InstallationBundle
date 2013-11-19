<?php

/*
 * This file is part of the Claroline Connect package.
 *
 * (c) Claroline Consortium <consortium@claroline.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Claroline\InstallationBundle\Fixtures;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;

/**
 * Class responsible for loading the data fixtures of a bundle.
 */
class FixtureLoader
{
    private $container;
    private $executor;

    /**
     * Constructor.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     * @param \Doctrine\Common\DataFixtures\Executor\ORMExecutor $executor
     */
    public function __construct(ContainerInterface $container, ORMExecutor $executor)
    {
        $this->container = $container;
        $this->executor = $executor;
    }

    /**
     * Loads the fixtures of a bundle. Fixtures are expected to be found in a
     * "DataFixtures/ORM" or "DataFixtures" directory within the bundle. Note
     * that fixtures are always appended (no purge/truncation).
     *
     * @param \Symfony\Component\HttpKernel\Bundle\Bundle $bundle
     *
     * @return boolean True if some fixtures have been found and executed, false otherwise
     */
    public function load(BundleInterface $bundle, $fixturesDirectory = 'DataFixtures')
    {
        // we must get a fresh instance of the loader (scope = prototype)
        // to avoid re-executing previously loaded fixtures
        $loader = $this->container->get('claroline.symfony_fixture_loader');
        $directory = "{$bundle->getPath()}/{$fixturesDirectory}";
        $loader->loadFromDirectory($directory);
        $fixtures = $loader->getFixtures();

        if ($fixtures) {
            $this->executor->execute($fixtures, true);

            return true;
        }

        return false;
    }
}
