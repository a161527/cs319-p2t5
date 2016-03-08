<?php

namespace App\Utility;

class RoleNames {
    public static function ConferenceManager($confId) {
        return "conference-manager." . $confId;
    }
}
