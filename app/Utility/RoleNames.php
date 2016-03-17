<?php

namespace App\Utility;

/**
 * Utility for getting role names.
 */
class RoleNames {
    public static function ConferenceManager($confId) {
        return "conference-manager." . $confId;
    }

    public static function EventManager($eventId) {
    return "event-manager." . $eventId;
    }
}
