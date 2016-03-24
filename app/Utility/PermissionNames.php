<?php

namespace App\Utility;

/**
 * Provides functions to get the names for permissions.  This
 * is essentially used to centralize names, and also to avoid issues
 * constructing permission names for specific conferences/events.
 */
class PermissionNames {
    //=============GLOBAL PERMISSIONS===============
    public static function CreateConference() {
        return "create-conference";
    }

    public static function ManageGlobalPermissions() {
        return "manage-global-permissions";
    }

    public static function ApproveUserRegistration() {
        return "approve-user-registration";
    }

    public static function ViewSiteStatistics() {
        return "view-site-statistics";
    }


    //==============CONFERENCE PERMISSIONS===========
    public static function ConferenceEventCreate($confId) {
        return "conference-event-create." . $confId;
    }

    public static function ConferenceRegistrationApproval($confId) {
        return "conference-registration-approval." . $confId;
    }

    public static function ConferencePermissionManagement($confId) {
        return "conference-permission-management." . $confId;
    }

    public static function ConferenceInfoEdit($confId) {
        return "conference-info-edit." . $confId;
    }

    public static function ConferenceInventoryEdit($confId) {
        return "conference-inventory-edit." . $confId;
    }

    public static function ConferenceRoomEdit($confId) {
        return "conference-room-edit." . $confId;
    }

    public static function ConferenceTransportationEdit($confId) {
        return "conference-transportation-edit." . $confId;
    }

    public static function ConferenceExternalTransportView($confId) {
        return "conference-external-transport-view." . $confId;
    }

    public static function ConferenceAnnounce($confId) {
        return "conference-announce." . $confId;
    }

    public static function ConferenceViewStatistics($confId) {
        return "conference-view-statistics." . $confId;
    }

    public static function AllConferencePermissions($confId) {
        return [
                self::ConferenceEventCreate($confId),
                self::ConferenceRegistrationApproval($confId),
                self::ConferencePermissionManagement($confId),
                self::ConferenceInfoEdit($confId),
                self::ConferenceInventoryEdit($confId),
                self::ConferenceRoomEdit($confId),
                self::ConferenceTransportationEdit($confId),
                self::ConferenceAnnounce($confId),
                self::ConferenceViewStatistics($confId)];
    }

    //================EVENT PERMISSIONS============
    public static function EventInfoEdit($evtId) {
        return "event-info-edit." . $evtId;
    }

    public static function EventDetailView($evtId) {
        return "event-detail-view." . $evtId;
    }

    public static function EventAnnounce($evtId) {
        return "event-announce." . $evtId;
    }

    public static function AllEventPermissions($evtId) {
        return [
            self::EventInfoEdit($evtId),
            self::EventDetailView($evtId),
            self::EventAnnounce($evtId)];
    }

    public static function normalizeConferencePermission($permName) {
        return explode(".", $permName)[0];
    }
}
