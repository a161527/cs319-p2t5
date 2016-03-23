#### /api/conference

###### GET

Gets a complete list of all conferences.  Format will look something like

```
[{
    "id":<number>,
    "name":<string>,
    "start":<date>,
    "end":<date>,
    "location":<string>,
    "description":<string|null>,
    "hasTransportation":<boolean>,
    "hasAccommodations":<boolean>,
}, ...]
```

This endpoint doesn't do any authentication - hitting it without a token will
(currently) work just fine.  However, that might need to change at some point.

###### POST

Creates a new conference.  Requires the user to have the `conference-create` permission.
Request body should be a single conference in the same format as the conferences
returned by GET requests to this endpoint, but without `id`. (Not sure
if that would break or not, it may just not care).

#### /api/conference/{confId}

confId: An integer identifier for the conference to work with

###### GET

Get the conference info.  Returns a single json object in the same format as the objects
from `/api/conferences`. Does not perform authentication (currently).

###### PUT

Edit the conference info.  Same format as POST requests to `/api/conferences`.  Requires
the user to have the `conference-info-edit` permission for the current conference.

###### DELETE

Delete the conference.  Requires the user have the ConferenceManager role. (Do we want to change that?)

#### /api/conferences/{confId}/register

###### POST

Request registration in the conference specified by `confId`.  Takes a single json object with the format

```
{
    "attendees":[<integer>, ...],
    "needsTransportation":<boolean>,
    "hasFlight":<boolean>,
    "flight": {
        "number":<integer>,
        "arrivalDate":<date>,
        "arrivalTime":<date>,
        "airline":<string>,
        "airport":<string>
    }
}
```

Where `flight` (all fields) is required unless `hasFlight` is set to false.  If flight is filled in,
hasFlight can be omitted.

Requires that all the user IDs in `attendees` be IDs of users attached to the current account.
If they are not, returns a 403 error.

Should also check that all dependents are approved. (Don't think it does that yet).


Returns a list of the IDs of all created registration requests (one is created per user, need another endpoint for aggregating them).

#### /api/conferences/{confId}/register/{registryId}

confId: Conference ID
registryId: Registration ID

###### GET

Returns information about a registration request.  Format:

```
{
    "needsTransportation":<boolean>,
    "approved":<boolean>,
    "attendee":<string>,
    "flight":...,
    "access":<"full" | "edit">
}
```
`flight` has the same format as flights given to `/api/conferences/{confId}/register`.

Requires that the current account either:
* Is the account that owns the attendee user
* Has the `conference-registration-approval` permission for the current conference.

#### /api/conferences/{confId}/register/{registryId}/approve

Approves the specified registration request.  Requires that the current account
have the `conference-registration-approval` permission for the current conference.
