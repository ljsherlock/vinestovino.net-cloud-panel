<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit652b5f799179a11ad272e0a00ac2967d
{
    public static $prefixLengthsPsr4 = array (
        'D' => 
        array (
            'DASPRiD\\Enum\\' => 13,
        ),
        'B' => 
        array (
            'BaconQrCode\\' => 12,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'DASPRiD\\Enum\\' => 
        array (
            0 => __DIR__ . '/..' . '/dasprid/enum/src',
        ),
        'BaconQrCode\\' => 
        array (
            0 => __DIR__ . '/..' . '/bacon/bacon-qr-code/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit652b5f799179a11ad272e0a00ac2967d::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit652b5f799179a11ad272e0a00ac2967d::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit652b5f799179a11ad272e0a00ac2967d::$classMap;

        }, null, ClassLoader::class);
    }
}
