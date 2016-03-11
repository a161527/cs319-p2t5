<?php

namespace App\Utility;

/**
 * Provides functions to get the names for permissions.  This
 * is essentially used to centralize names, and also to avoid issues
 * constructing permission names for specific conferences/events.
 */
class PermissionNames {
    public static function ConferenceCreate() {
        return "create-conference";
    }

    public static function ConferenceEventCreate($confId) {
        return "conference-event-create." . $confId;
    }

    public static function ConferenceRegistrationApproval($confId) {
        return "conference-registration-approval." . $confId;
    }

    public static function ConferenceInfoEdit($confId) {
        return "conference-info-edit." . $confId;
    }
}
