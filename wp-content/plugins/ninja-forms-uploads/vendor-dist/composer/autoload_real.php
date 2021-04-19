<?php

// autoload_real.php @generated by Composer

class ComposerAutoloaderInit97640429974b9d140ef221511b978bde
{
    private static $loader;

    public static function loadClassLoader($class)
    {
        if ('NF_FU_VENDOR\Composer\Autoload\ClassLoader' === $class) {
            require __DIR__ . '/ClassLoader.php';
        }
    }

    /**
     * @return \NF_FU_VENDOR\Composer\Autoload\ClassLoader
     */
    public static function getLoader()
    {
        if (null !== self::$loader) {
            return self::$loader;
        }

        spl_autoload_register(array('ComposerAutoloaderInit97640429974b9d140ef221511b978bde', 'loadClassLoader'), true, true);
        self::$loader = $loader = new \NF_FU_VENDOR\Composer\Autoload\ClassLoader();
        spl_autoload_unregister(array('ComposerAutoloaderInit97640429974b9d140ef221511b978bde', 'loadClassLoader'));

        $useStaticLoader = PHP_VERSION_ID >= 50600 && !defined('HHVM_VERSION') && (!function_exists('zend_loader_file_encoded') || !zend_loader_file_encoded());
        if ($useStaticLoader) {
            require_once __DIR__ . '/autoload_static.php';

            call_user_func(\NF_FU_VENDOR\Composer\Autoload\ComposerStaticInit97640429974b9d140ef221511b978bde::getInitializer($loader));
        } else {
            $classMap = require __DIR__ . '/autoload_classmap.php';
            if ($classMap) {
                $loader->addClassMap($classMap);
            }
        }

        $loader->setClassMapAuthoritative(true);
        $loader->register(true);

        if ($useStaticLoader) {
            $includeFiles = NF_FU_VENDOR\Composer\Autoload\ComposerStaticInit97640429974b9d140ef221511b978bde::$files;
        } else {
            $includeFiles = require __DIR__ . '/autoload_files.php';
        }
        foreach ($includeFiles as $fileIdentifier => $file) {
            composerRequire97640429974b9d140ef221511b978bde($fileIdentifier, $file);
        }

        return $loader;
    }
}

function composerRequire97640429974b9d140ef221511b978bde($fileIdentifier, $file)
{
    if (empty($GLOBALS['__composer_autoload_files'][$fileIdentifier])) {
        require $file;

        $GLOBALS['__composer_autoload_files'][$fileIdentifier] = true;
    }
}