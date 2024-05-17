<?php

declare(strict_types=1);

require __DIR__ . "/../vendor/autoload.php";

use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\Node\Stmt;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter;

const EXPORT_DIR = 'Export';

// Parser for the version you are running on.
$parser = (new ParserFactory())->createForHostVersion();

$parser = (new ParserFactory())->createForHostVersion();
$traverser = new NodeTraverser;
$prettyPrinter = new PrettyPrinter\Standard;

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

print " [OK] No errors\n";
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
                if ($prts[1] !== $module) {
                    if (count($prts) === 2 || $prts[2] !== EXPORT_DIR) {
                        $errors[] = [
                            'type' => 'use',
                            'lineNumber' => $use->name->getLine(),
                            'classPath' => $use->name->name,
                            ];
                    }
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
        if ($error['type'] === 'use') {
            printf("  %-4d  'use' statement breaks module boundary. Imported class/namespace: %s\n", $error['lineNumber'], $error['classPath']);
        }
    }
    print " ------ -----------------------------------------------------------------------------------------\n\n";
}
