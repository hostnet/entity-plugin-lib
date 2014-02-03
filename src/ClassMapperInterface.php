<?php
namespace Hostnet\Component\EntityPlugin;

/**
 * Use this guy when you want to find classes.
 * Abstracts the filesystem away.
 */
interface ClassMapperInterface
{

    /**
     * Creates a hashmap of which classes exist in given path
     *
     * @return array { class => path }
     */
    public function createClassMap($path);
}
