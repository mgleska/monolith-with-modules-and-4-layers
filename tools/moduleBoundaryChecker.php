<?php

declare(strict_types=1);

require __DIR__ . "/../vendor/autoload.php";

use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\Node\Stmt;
use PhpParser\ParserFactory;

const EXPORT_DIR_1 = '_2_Export';
const EXPORT_DIR_2 = 'Export';
const CONNECTOR_DIR_1 = '_1_Connector';
const CONNECTOR_DIR_2 = 'Connector';
const COMMON_INFRASTRUCTURE_DIR = 'CommonInfrastructure';

$parser = (new ParserFactory())->createForHostVersion();
$traverser = new NodeTraverser;
$traverser->addVisitor(new NameResolver); // we will need resolved names

$inDir = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'src';

$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($inDir));
$files = new RegexIterator($files, '/\.php$/');

$totalCount = 0;

foreach ($files as $file) {
    try {
        $stmts = $parser->parse(file_get_contents($file->getPathname()));
        /** @var Stmt[] $stmts */
        $stmts = $traverser->traverse($stmts);

        $errors = analyze($stmts);
        if ($errors) {
            display(substr($file->getPathname(), strlen($inDir) - 3), $errors);
            $totalCount += count($errors);
        }
    }
    catch (Error $e) {
        print $file . "\n";
        echo 'Parse Error: ', $e->getMessage(), "\n";
    }
}

if ($totalCount > 0) {
    printf(" [ERROR] Found %d errors\n", $totalCount);
    exit(1);
}

print "\n [OK] No errors\n\n";
exit(0);


/**
 * @param Stmt[] $stmts
 * @return array<int, array<string, string|integer>>
 */
function analyze(array $stmts): array
{
    $errors = [];

    foreach ($stmts as $st) {
        if (! $st instanceof Stmt\Namespace_) {
            continue;
        }

        $prts = $st->name->getParts();

        if (count($prts) <= 1) {
            continue;
        }

        $app = $prts[0];
        $module = $prts[1];
        $layer = $prts[2] ?? '';

        foreach ($st->stmts as $stl2) {
            if (! $stl2 instanceof Stmt\Use_) {
                continue;
            }

            foreach ($stl2->uses as $use) {
                $prts = $use->name->getParts();
                if (count($prts) <= 1) {
                    continue;
                }
                if ($prts[0] !== $app) {
                    continue;
                }
                if ($prts[1] === COMMON_INFRASTRUCTURE_DIR) {
                    continue;
                }
                if ($prts[1] !== $module) {
                    if (count($prts) === 2 || ! ($prts[2] === EXPORT_DIR_1 || $prts[2] === EXPORT_DIR_2)) {
                        $errors[] = [
                            'type' => 'use-module',
                            'lineNumber' => $use->name->getLine(),
                            'classPath' => $use->name->name,
                            ];
                    }
                }
                if ($prts[1] === $module
                    && ($layer === CONNECTOR_DIR_1 || $layer === CONNECTOR_DIR_2)
                    && (count($prts) === 2
                         || ! ($prts[2] === EXPORT_DIR_1 || $prts[2] === EXPORT_DIR_2 || $prts[2] === CONNECTOR_DIR_1 || $prts[2] === CONNECTOR_DIR_2)
                       )
                   ) {
                    $errors[] = [
                        'type' => 'use-layer-1',
                        'lineNumber' => $use->name->getLine(),
                        'classPath' => $use->name->name,
                    ];
                }
                if ($prts[1] === $module
                    && ($layer === EXPORT_DIR_1 || $layer === EXPORT_DIR_2)
                    && (count($prts) === 2 || ! ($prts[2] === EXPORT_DIR_1 || $prts[2] === EXPORT_DIR_2))
                ) {
                    $errors[] = [
                        'type' => 'use-layer-2',
                        'lineNumber' => $use->name->getLine(),
                        'classPath' => $use->name->name,
                    ];
                }
            }
        }
    }

    return $errors;
}

/**
 * @param array<int, array<string, string|integer>> $errors
 */
function display(string $file, array $errors): void
{
    print " ------ -----------------------------------------------------------------------------------------\n";
    print "  Line  " . $file . "\n";
    print " ------ -----------------------------------------------------------------------------------------\n";
    foreach ($errors as $error) {
        switch ($error['type']) {
            case 'use-module':
                printf("  %-4d  'use' statement breaks module boundary. Imported class/namespace: %s\n", $error['lineNumber'], $error['classPath']);
                break;
            case 'use-layer-1':
                printf("  %-4d  'use' statement at layer 1 loads class from layer below 2. Imported class/namespace: %s\n", $error['lineNumber'], $error['classPath']);
                break;
            case 'use-layer-2':
                printf("  %-4d  'use' statement at layer 2 loads class from other layer. Imported class/namespace: %s\n", $error['lineNumber'], $error['classPath']);
                break;
        }
    }
    print " ------ -----------------------------------------------------------------------------------------\n\n";
}
