<?php

class TwigLoader implements \Twig_LoaderInterface
{
    protected static $paths;
    protected static $filename;

    public function __construct($paths, $filename) {
        self::$paths = explode(',', $paths);
        self::$filename = $filename;
    }

    public function getSource($name) {
        if (!self::getTemplatePath($name)) {
            return;
        }

        return new \Twig_Source(file_get_contents(self::getTemplatePath($name)) , $name);
    }

    public function getSourceContext($name) {
        return self::getSource($name);
    }

    public function getCacheKey($name) {
        return $name;
    }

    public function isFresh($name, $time) {
        return true;
    }

    public function exists($name) {
        if (self::getTemplatePath($name)) {
            return true;
        }
        return false;
    }

    public function getTemplatePath($name) {
        foreach (self::$paths as $path) {
            $fullpath = get_stylesheet_directory() . DIRECTORY_SEPARATOR . $path . DIRECTORY_SEPARATOR . $name . DIRECTORY_SEPARATOR . self::$filename;

            if (is_file($fullpath)) {
                return $fullpath;
            }
        }

        return false;
    }
}
