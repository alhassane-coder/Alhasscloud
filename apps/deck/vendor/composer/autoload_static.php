<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit45634ef9840d68c1607c1abea5bd923a
{
    public static $prefixesPsr0 = array (
        'c' => 
        array (
            'cogpowered\\FineDiff' => 
            array (
                0 => __DIR__ . '/..' . '/cogpowered/finediff/src',
            ),
        ),
    );

    public static $classMap = array (
        'cogpowered\\FineDiff\\Delimiters' => __DIR__ . '/..' . '/cogpowered/finediff/src/cogpowered/FineDiff/Delimiters.php',
        'cogpowered\\FineDiff\\Diff' => __DIR__ . '/..' . '/cogpowered/finediff/src/cogpowered/FineDiff/Diff.php',
        'cogpowered\\FineDiff\\Exceptions\\GranularityCountException' => __DIR__ . '/..' . '/cogpowered/finediff/src/cogpowered/FineDiff/Exceptions/GranularityCountException.php',
        'cogpowered\\FineDiff\\Exceptions\\OperationException' => __DIR__ . '/..' . '/cogpowered/finediff/src/cogpowered/FineDiff/Exceptions/OperationException.php',
        'cogpowered\\FineDiff\\Granularity\\Character' => __DIR__ . '/..' . '/cogpowered/finediff/src/cogpowered/FineDiff/Granularity/Character.php',
        'cogpowered\\FineDiff\\Granularity\\Granularity' => __DIR__ . '/..' . '/cogpowered/finediff/src/cogpowered/FineDiff/Granularity/Granularity.php',
        'cogpowered\\FineDiff\\Granularity\\GranularityInterface' => __DIR__ . '/..' . '/cogpowered/finediff/src/cogpowered/FineDiff/Granularity/GranularityInterface.php',
        'cogpowered\\FineDiff\\Granularity\\Paragraph' => __DIR__ . '/..' . '/cogpowered/finediff/src/cogpowered/FineDiff/Granularity/Paragraph.php',
        'cogpowered\\FineDiff\\Granularity\\Sentence' => __DIR__ . '/..' . '/cogpowered/finediff/src/cogpowered/FineDiff/Granularity/Sentence.php',
        'cogpowered\\FineDiff\\Granularity\\Word' => __DIR__ . '/..' . '/cogpowered/finediff/src/cogpowered/FineDiff/Granularity/Word.php',
        'cogpowered\\FineDiff\\Parser\\Opcodes' => __DIR__ . '/..' . '/cogpowered/finediff/src/cogpowered/FineDiff/Parser/Opcodes.php',
        'cogpowered\\FineDiff\\Parser\\OpcodesInterface' => __DIR__ . '/..' . '/cogpowered/finediff/src/cogpowered/FineDiff/Parser/OpcodesInterface.php',
        'cogpowered\\FineDiff\\Parser\\Operations\\Copy' => __DIR__ . '/..' . '/cogpowered/finediff/src/cogpowered/FineDiff/Parser/Operations/Copy.php',
        'cogpowered\\FineDiff\\Parser\\Operations\\Delete' => __DIR__ . '/..' . '/cogpowered/finediff/src/cogpowered/FineDiff/Parser/Operations/Delete.php',
        'cogpowered\\FineDiff\\Parser\\Operations\\Insert' => __DIR__ . '/..' . '/cogpowered/finediff/src/cogpowered/FineDiff/Parser/Operations/Insert.php',
        'cogpowered\\FineDiff\\Parser\\Operations\\OperationInterface' => __DIR__ . '/..' . '/cogpowered/finediff/src/cogpowered/FineDiff/Parser/Operations/OperationInterface.php',
        'cogpowered\\FineDiff\\Parser\\Operations\\Replace' => __DIR__ . '/..' . '/cogpowered/finediff/src/cogpowered/FineDiff/Parser/Operations/Replace.php',
        'cogpowered\\FineDiff\\Parser\\Parser' => __DIR__ . '/..' . '/cogpowered/finediff/src/cogpowered/FineDiff/Parser/Parser.php',
        'cogpowered\\FineDiff\\Parser\\ParserInterface' => __DIR__ . '/..' . '/cogpowered/finediff/src/cogpowered/FineDiff/Parser/ParserInterface.php',
        'cogpowered\\FineDiff\\Render\\Html' => __DIR__ . '/..' . '/cogpowered/finediff/src/cogpowered/FineDiff/Render/Html.php',
        'cogpowered\\FineDiff\\Render\\Renderer' => __DIR__ . '/..' . '/cogpowered/finediff/src/cogpowered/FineDiff/Render/Renderer.php',
        'cogpowered\\FineDiff\\Render\\RendererInterface' => __DIR__ . '/..' . '/cogpowered/finediff/src/cogpowered/FineDiff/Render/RendererInterface.php',
        'cogpowered\\FineDiff\\Render\\Text' => __DIR__ . '/..' . '/cogpowered/finediff/src/cogpowered/FineDiff/Render/Text.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixesPsr0 = ComposerStaticInit45634ef9840d68c1607c1abea5bd923a::$prefixesPsr0;
            $loader->classMap = ComposerStaticInit45634ef9840d68c1607c1abea5bd923a::$classMap;

        }, null, ClassLoader::class);
    }
}
