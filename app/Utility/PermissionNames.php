<?php

namespace App\Utility;

class PermissionNames {
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
