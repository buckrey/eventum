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

namespace Eventum\Console\Command;

use Eventum\Db\Doctrine;
use Eventum\Export\IssueExport;
use Symfony\Component\Console\Output\OutputInterface;

class ExportIssuesCommand extends Command
{
    const DEFAULT_COMMAND = 'export:issues';
    const USAGE = self::DEFAULT_COMMAND . ' [issueId] [filename]';

    public function execute(OutputInterface $output, int $issueId, ?string $fileName): void
    {
        $this->output = $output;

        if ($issueId) {
            $this->exportIssue($issueId, $fileName ?: 'output.csv');
        }
    }

    private function exportIssue(int $issueId, string $fileName): void
    {
        $repo = Doctrine::getIssueRepository();
        $issue = $repo->findById($issueId);

        $exporter = new IssueExport($fileName);
        $exporter->export($issue);
    }
}
