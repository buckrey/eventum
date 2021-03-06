<?php

/*
 * This file is part of the Eventum (Issue Tracking System) package.
 *
 * @copyright (c) Eventum Team
 * @license GNU General Public License, version 2 or later (GPL-2+)
 *
 * For the full copyright and license information,
 * please see the COPYING and AUTHORS files
 * that were distributed with this source code.
 */

namespace Eventum\Test;

use Eventum\Markdown;
use Generator;

/**
 * @group db
 */
class MarkdownTest extends TestCase
{
    /** @var Markdown */
    private $renderer;

    private function getRenderer(): Markdown
    {
        static $renderer;

        return $renderer ?: new Markdown();
    }

    public function setUp(): void
    {
        $this->renderer = $this->getRenderer();
    }

    /**
     * @dataProvider dataProvider
     */
    public function testMarkdown(string $input, string $expected): void
    {
        $rendered = $this->renderer->render($input);
        $this->assertEquals($expected, $rendered);
    }

    public function dataProvider(): Generator
    {
        $testNames = [
            'autolink',
            'h5-details',
            'headers',
            'inline',
            'linkrefs',
            'table',
            'tasklist',
            'userhandle',
        ];

        foreach ($testNames as $testName) {
            yield $testName => [
                $this->readDataFile("markdown/$testName.md"),
                $this->readDataFile("markdown/$testName.html"),
            ];
        }
    }
}
