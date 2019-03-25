<?php
namespace Hostnet\Component\EntityPlugin\Compound;

use Composer\IO\IOInterface;
use Hostnet\Component\EntityPlugin\EntityPackage;
use Hostnet\Component\EntityPlugin\PackageClass;
use Symfony\Component\Filesystem\Filesystem;
use Twig\Environment;

/**
 * The generator for pass 1/3: Generating compound traits and interfaces
 *
 * It generates the combined entity and repository traits
 * Generated/ClientTrait and Generated/ClientRepositoryTrait
 *
 * @author Nico Schoenmaker <nschoenmaker@hostnet.nl>
 */
class CompoundGenerator
{

    private $io;

    private $environment;

    private $filesystem;

    private $content_provider;

    /**
     * @param IOInterface $io
     * @param Environment $environment
     * @param Filesystem $filesystem
     * @param PackageContentProvider $content_provider
     */
    public function __construct(
        IOInterface $io,
        Environment $environment,
        Filesystem $filesystem,
        PackageContentProvider $content_provider
    ) {
        $this->io               = $io;
        $this->environment      = $environment;
        $this->filesystem       = $filesystem;
        $this->content_provider = $content_provider;
    }

    /**
     * Ask the generator to generate all the trait of traits, and their matching combined interfaces
     *
     * @return void
     */
    public function generate(EntityPackage $entity_package)
    {
        $classes = $this->content_provider->getPackageContent($entity_package)->getClasses();
        foreach ($classes as $package_class) {
            /* @var $package_class PackageClass */
            $this->writeIfDebug(
                '        - Finding traits for <info>' . $package_class->getName() . '</info>.'
            );
            $traits = $this->recursivelyFindUseStatementsFor($entity_package, $package_class);
            $this->generateTrait($package_class, array_unique($traits, SORT_REGULAR));
        }
    }

    /**
     * In all other cases within this class, we use the PackageContentProvider to get the package
     * content. In this case we bypass it since we really want to know whether the entity exists.
     * @param EntityPackage $entity_package
     * @param string $requirement
     * @return boolean
     */
    private function doesEntityExistInTree(EntityPackage $entity_package, $requirement)
    {
        foreach ($entity_package->getFlattenedRequiredPackages() as $required_entity_package) {
            if ($required_entity_package->getEntityContent()->hasClass($requirement)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Gives all the entities to be required in the compound interface
     * Also generates a unique alias for them
     *
     * @param EntityPackage $entity_package
     * @param PackageClass $package_class
     * @param array $checked list of checked packages to prevent recursion errors
     * @return UseStatement[]
     */
    private function recursivelyFindUseStatementsFor(
        EntityPackage $entity_package,
        PackageClass $package_class,
        array &$checked = []
    ) {
        $result              = [];
        $entity_package_name = $entity_package->getPackage()->getName();

        if (isset($checked[$entity_package_name])) {
            return $result;
        } else {
            $checked[$entity_package_name] = true;
        }

        foreach ($entity_package->getDependentPackages() as $name => $dependent_package) {
            /* @var $dependent_package EntityPackage */
            $use_statements = $this->recursivelyFindUseStatementsFor($dependent_package, $package_class, $checked);
            $result         = array_merge($result, $use_statements);
        }

        $this->writeIfDebug(
            '          - Scanning for traits in <info>' . $entity_package->getPackage()->getName() . '</info>.'
        );

        $contents = $this->content_provider->getPackageContent($entity_package);
        $traits   = $contents->getOptionalTraits($package_class->getShortName());

        foreach ($traits as $trait) {
            /* @var $trait \Hostnet\Component\EntityPlugin\OptionalPackageTrait */
            $requirement = $trait->getRequirement();
            if ($this->doesEntityExistInTree($entity_package, $requirement)) {
                $result[] = $trait;
                $this->writeIfDebug(
                    '            Injected <info>' . $trait->getName() .   '</info> from <info>' .
                    $entity_package->getPackage()->getName() .
                    '</info>.'
                );
            } else {
                $this->writeIfDebug(
                    '            Not injected <warn>' . $trait->getName() .   '</warn> from <info>' .
                    $entity_package->getPackage()->getName() . ' because ' . $requirement . ' is not found</info>.'
                );
            }
        }

        $package_class = $contents->getClassOrTrait($package_class->getShortName());
        if ($package_class) {
            $result[] = $package_class;
            $this->writeIfDebug(
                '            Found <info>' . $package_class->getName() . '</info> in <info>'
                . $entity_package->getPackage()->getName() . '</info>.'
            );
        }
        return $result;
    }

    /**
     * Generates Generated/<class_name>Traits.php
     *
     * @param PackageClass $package_class
     * @param array $traits
     */
    private function generateTrait(PackageClass $package_class, array $traits)
    {
        $short_name = $package_class->getShortName();

        $this->writeIfVeryVerbose(
            '        - Generating trait of traits for <info>' . $package_class->getName() . '</info>'
        );

        $generated_namespace = $package_class->getGeneratedNamespaceName();

        $use_statements = array_filter(
            $traits,
            function (PackageClass $stmt) {
                return $stmt->isTrait();
            }
        );
        sort($use_statements);

        $data = $this->environment->render(
            'trait.php.twig',
            [
                'class_name' => $short_name,
                'namespace' => $generated_namespace,
                'use_statements' => $use_statements
            ]
        );

        $this->filesystem->dumpFile(
            $package_class->getGeneratedDirectory() . $short_name . 'Trait.php',
            $data
        );
    }

    private function writeIfVeryVerbose($text)
    {
        if ($this->io->isVeryVerbose()) {
            $this->io->write($text);
        }
    }

    private function writeIfDebug($text)
    {
        if ($this->io->isDebug()) {
            $this->io->write($text);
        }
    }
}
