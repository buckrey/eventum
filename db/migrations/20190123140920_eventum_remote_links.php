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

use Eventum\Db\AbstractMigration;

class EventumRemoteLinks extends AbstractMigration
{
    public function change()
    {
        $this->table('remote_links', ['id' => false, 'primary_key' => 'rel_id', 'collation' => self::COLLATION_ASCII])
            ->addColumn('rel_id', 'integer', ['length' => 10, 'signed' => false, 'identity' => true])
            ->addColumn('rel_gid', 'string', ['null' => true, 'limit' => self::TEXT_SMALL])
            ->addColumn('rel_relationship', 'string', ['null' => true, 'limit' => self::TEXT_SMALL])
            ->addColumn('rel_url', 'text', ['null' => true, 'limit' => self::TEXT_REGULAR])
            ->addColumn('rel_title', 'string', ['null' => true, 'limit' => self::TEXT_SMALL])
            ->addIndex(['rel_id', 'rel_gid'])
            ->create();
    }
}
