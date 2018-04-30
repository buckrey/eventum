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

namespace Eventum\Event;

use Group;
use Issue;
use Notification;
use Reminder;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @deprecated
 *
 * events listened, created from this class need to be implemented in user subscriber classes
 * will be dropped from eventum core at some point
 */
class IrcLegacySubscriber implements EventSubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            SystemEvents::IRC_NOTIFY_BLOCKED_MESSAGE => 'notifyBlockedMessage',
            SystemEvents::NOTIFY_ISSUE_CREATED => 'notifyIssueCreated',
            SystemEvents::REMINDER_ACTION_PERFORM => 'reminderAction',
        ];
    }

    /**
     * Method used to send an IRC notification about a blocked email that was
     * saved into an internal note.
     *
     * @deprecated implement the logic in your own Subscriber
     */
    public function notifyBlockedMessage(GenericEvent $event)
    {
        if ($event['irc_legacy_handled']) {
            return;
        }

        $issue_id = $event['issue_id'];
        $from = $event['from'];

        $notice = "Issue #$issue_id updated (";
        // also add information about the assignee, if any
        $assignment = Issue::getAssignedUsers($issue_id);
        if (count($assignment) > 0) {
            $notice .= 'Assignment: ' . implode(', ', $assignment) . '; ';
        }
        $notice .= "BLOCKED email from '$from')";

        $prj_id = Issue::getProjectID($issue_id);
        Notification::notifyIRC($prj_id, $notice, $issue_id);
    }

    /**
     * @param GenericEvent $event
     * @deprecated implement the logic in your own Subscriber
     */
    public function reminderAction(GenericEvent $event)
    {
        if ($event['irc_legacy_handled']) {
            return;
        }

        $issue_id = $event['issue_id'];
        $action = $event['action'];

        // alert IRC if needed
        if (!$action['rma_alert_irc']) {
            return;
        }

        $irc_notice = "Issue #$issue_id (";
        if (!empty($data['pri_title'])) {
            $irc_notice .= 'Priority: ' . $data['pri_title'];
        }
        if (!empty($data['sev_title'])) {
            $irc_notice .= 'Severity: ' . $data['sev_title'];
        }
        // also add information about the assignee, if any
        $assignment = Issue::getAssignedUsers($issue_id);
        if (count($assignment) > 0) {
            $irc_notice .= '; Assignment: ' . implode(', ', $assignment);
        }
        if (!empty($data['iss_grp_id'])) {
            $irc_notice .= '; Group: ' . Group::getName($data['iss_grp_id']);
        }
        $irc_notice .= "), Reminder action '" . $action['rma_title'] . "' was just triggered; " . $action['rma_boilerplate'];

        $prj_id = Issue::getProjectID($issue_id);
        Notification::notifyIRC($prj_id, $irc_notice, $issue_id, false, APP_EVENTUM_IRC_CATEGORY_REMINDER);
    }

    /**
     * Notify new issue to irc channel
     *
     * @param GenericEvent $event
     * @deprecated implement the logic in your own Subscriber
     */
    public function notifyIssueCreated(GenericEvent $event)
    {
        if ($event['irc_legacy_handled']) {
            return;
        }

        $issue_id = $event['issue_id'];
        $prj_id = $event['prj_id'];
        $data = $event['data'];

        $irc_notice = "New Issue #$issue_id (";
        $quarantine = Issue::getQuarantineInfo($issue_id);
        if ($quarantine) {
            $irc_notice .= 'Quarantined; ';
        }

        $irc_notice .= 'Priority: ' . $data['pri_title'];

        // also add information about the assignee, if any
        $assignment = Issue::getAssignedUsers($issue_id);
        if (count($assignment) > 0) {
            $irc_notice .= '; Assignment: ' . implode(', ', $assignment);
        }

        if (!empty($data['iss_grp_id'])) {
            $irc_notice .= '; Group: ' . Group::getName($data['iss_grp_id']);
        }
        $irc_notice .= '), ';

        if (isset($data['customer'])) {
            $irc_notice .= $data['customer']['name'] . ', ';
        }

        $irc_notice .= $data['iss_summary'];

        Notification::notifyIRC($prj_id, $irc_notice, $issue_id, false, false, 'new_issue');
    }
}
