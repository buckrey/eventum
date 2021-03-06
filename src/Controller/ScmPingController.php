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

namespace Eventum\Controller;

use Eventum\Scm;
use LogicException;
use Throwable;

class ScmPingController extends BaseController
{
    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
    }

    /**
     * {@inheritdoc}
     */
    protected function canAccess(): bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    protected function defaultAction(): void
    {
        try {
            ob_start();
            $this->process();
            $status = [
                'code' => 0,
                'message' => ob_get_clean(),
            ];
        } catch (Throwable $e) {
            header('HTTP/1.0 500');
            $code = $e->getCode();
            $status = [
                'code' => $code && is_numeric($code) ? $code : -1,
                'message' => $e->getMessage(),
            ];

            if ($e instanceof LogicException) {
                // LogicException subclasses are expected, not really errors
                $this->logger->warning($e);
            } else {
                $this->logger->error($e);
            }
        }

        echo json_encode($status);
    }

    private function process(): void
    {
        // NOTE: output is captured from all adapters
        // but if exception is thrown. not all adapters are processed
        foreach ($this->getAdapters() as $adapter) {
            if ($adapter->can()) {
                $adapter->process();
            }
        }
    }

    /**
     * @return \Eventum\Scm\Adapter\AdapterInterface[]
     */
    private function getAdapters()
    {
        $request = $this->getRequest();

        return [
            new Scm\Adapter\Gitlab($request, $this->logger),
            new Scm\Adapter\Cvs($request, $this->logger),
            new Scm\Adapter\Standard($request, $this->logger),
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function prepareTemplate(): void
    {
        // no template to render
        exit;
    }
}
