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

namespace Eventum\CustomField\Fields;

interface ListInterface extends CustomFieldInterface
{
    public function getList(int $fld_id, ?int $issue_id = null, ?string $form_type = null): array;
}
