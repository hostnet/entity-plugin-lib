<?php
use Hostnet\Entities\Installer\ReflectionGenerator;

use Symfony\Component\Finder\SplFileInfo;

use Hostnet\Entities\Installer\PackageIOInterface;

use Composer\IO\NullIO;

/**
 * More a functiononal test then a unit-test
 *
 * Tests (minimized versions of) cases that we've found in real-life
 * @author Nico Schoenmaker <nschoenmaker@hostnet.nl>
 */
class ReflectionGeneratorTest extends PHPUnit_Framework_TestCase
{
  /**
   * @dataProvider generateProvider
   * @param PackageIOInterface $package_io
   * @param SplFileInfo $file
   * @param string $expected
   */
  public function testGenerate($classname, SplFileInfo $file)
  {
    require_once(__DIR__ . '/EdgeCases/'.$classname.'.php');
    $io = new NullIO();
    $loader = new \Twig_Loader_Filesystem(__DIR__ . '/../../src/Hostnet/Entities/Resources/templates/');
    $environment = new \Twig_Environment($loader);

    $package_io = $this->getMock('Hostnet\Entities\Installer\PackageIOInterface');

    $that = $this;
    $package_io->expects($this->exactly(2))->method('writeGeneratedFile')->will($this->returnCallback(
        function($directory, $file, $data) use($that, $classname) {
          $that->assertEquals('Hostnet/EdgeCases/Entity', $directory);
          if($file === $classname.'TraitInterface.php') {
            $contents = file_get_contents(__DIR__ . '/EdgeCases/'.$classname.'TraitInterface.expected.php');
          } else if($file === 'Abstract'.$classname.'Trait.php') {
            $contents = file_get_contents(__DIR__ . '/EdgeCases/Abstract'.$classname.'Trait.expected.php');
          } else {
            $this->fail('Unexpected file '. $file);
          }
          $that->assertEquals($contents, $data);
    }));

    $generator = new ReflectionGenerator($io, $environment, $package_io, $file);
    $this->assertNull($generator->generate());
  }

  public function generateProvider()
  {
    $file = $this->getMockBuilder('Symfony\Component\Finder\SplFileInfo')->disableOriginalConstructor()->getMock();
    $file->expects($this->any())->method('getRelativePath')->will($this->returnValue('Hostnet/EdgeCases/Entity'));
    $file->expects($this->any())->method('getBasename')->will($this->returnValue('ConstructShouldNotBePresent'));

    $multiple_arguments_file = $this->getMockBuilder('Symfony\Component\Finder\SplFileInfo')->disableOriginalConstructor()->getMock();
    $multiple_arguments_file->expects($this->any())->method('getRelativePath')->will($this->returnValue('Hostnet/EdgeCases/Entity'));
    $multiple_arguments_file->expects($this->any())->method('getBasename')->will($this->returnValue('MultipleArguments'));

    $typed_parameters_file = $this->getMockBuilder('Symfony\Component\Finder\SplFileInfo')->disableOriginalConstructor()->getMock();
    $typed_parameters_file->expects($this->any())->method('getRelativePath')->will($this->returnValue('Hostnet/EdgeCases/Entity'));
    $typed_parameters_file->expects($this->any())->method('getBasename')->will($this->returnValue('TypedParameters'));

    return array(
        array('ConstructShouldNotBePresent', $file),
        array('MultipleArguments', $multiple_arguments_file),
        array('TypedParameters', $typed_parameters_file)
    );
  }

}