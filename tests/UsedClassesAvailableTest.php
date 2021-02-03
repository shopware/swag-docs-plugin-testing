<?php declare(strict_types=1);

namespace Swag\PluginTestingTests;

use Shopware\Core\Framework\Test\TestCaseBase\IntegrationTestBehaviour;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\Finder;

class UsedClassesAvailableTest extends TestCase
{
    use IntegrationTestBehaviour;

    public function testClassesAreInstantiable(): void
    {
        $namespace = str_replace('Tests', '', __NAMESPACE__);
        $counter = 0;
        $pluginClasses = $this->getPluginClasses();

        foreach ($pluginClasses as $class) {
            $path = explode(DIRECTORY_SEPARATOR, $class->getRelativePathname());
            unset($path[0]); // src folder

            $classRelativePath = str_replace(['.php', '/'], ['', '\\'], implode(DIRECTORY_SEPARATOR, $path));

            if (class_exists($namespace . '\\' . $classRelativePath)) {
                $counter += 1;
            }
        }

        $this->assertEquals(count($pluginClasses), $counter, "$counter classes loaded");
    }

    private function getPluginClasses(): Finder
    {
        $finder = new Finder();
        $finder->in(realpath(__DIR__ . '/../'));
        $finder->ignoreUnreadableDirs();
        $finder->exclude(['tests', 'vendor']);
        return $finder->files()->name('*.php');
    }
}
