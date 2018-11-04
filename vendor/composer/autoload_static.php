<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit7a224b48ac81cac6c0b60e261b8d8754
{
    public static $prefixLengthsPsr4 = array (
        'c' => 
        array (
            'cordmon\\' => 8,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'cordmon\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit7a224b48ac81cac6c0b60e261b8d8754::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit7a224b48ac81cac6c0b60e261b8d8754::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
