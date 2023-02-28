<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit3726155a4c680dcef172adae7d76cefb
{
    public static $prefixLengthsPsr4 = array (
        'M' => 
        array (
            'Mollie\\Api\\' => 11,
        ),
        'C' => 
        array (
            'Composer\\CaBundle\\' => 18,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Mollie\\Api\\' => 
        array (
            0 => __DIR__ . '/..' . '/mollie/mollie-api-php/src',
        ),
        'Composer\\CaBundle\\' => 
        array (
            0 => __DIR__ . '/..' . '/composer/ca-bundle/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit3726155a4c680dcef172adae7d76cefb::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit3726155a4c680dcef172adae7d76cefb::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit3726155a4c680dcef172adae7d76cefb::$classMap;

        }, null, ClassLoader::class);
    }
}
