<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit6aaaeff74500f96dffcb2d42e5a530c4
{
    public static $classMap = array (
        'Code_Inserter\\Admin\\Admin' => __DIR__ . '/../..' . '/admin/class-admin.php',
        'Code_Inserter\\Core\\Activator' => __DIR__ . '/../..' . '/core/class-activator.php',
        'Code_Inserter\\Core\\Deactivator' => __DIR__ . '/../..' . '/core/class-deactivator.php',
        'Code_Inserter\\Core\\I18n' => __DIR__ . '/../..' . '/core/class-i18n.php',
        'Code_Inserter\\Core\\Main' => __DIR__ . '/../..' . '/core/class-main.php',
        'Code_Inserter\\Front\\Front' => __DIR__ . '/../..' . '/front/class-front.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->classMap = ComposerStaticInit6aaaeff74500f96dffcb2d42e5a530c4::$classMap;

        }, null, ClassLoader::class);
    }
}
