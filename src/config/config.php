<?php
return [

    "enabled" => true, // Is cache enabled?

    "minutes" => 30, // Cache minutes.

    /**
     * If this event is triggered on this model,
     * the cache of that table is deleted.
     */
    "clear_events" => [
        "created",
        "updated",
        "deleted",
        "restored",

        //"hasManyCreated",
        "hasManyUpdated",
        "hasManyDeleted",
        "hasManyRestored",

        "belongsToUpdated",
        "belongsToAssociated",
        "belongsToDissociated",

        //"belongsToManyCreated",
        "belongsToManyAttached",
        "belongsToManyDetached",
        "belongsToManySynced",

        "morphToAttached",
        "morphToDetached",
        "morphToUpdated",

        "morphByManyAttached",
        "morphByManyDetached",
        "morphByManySynced",
        "morphByManyToggled",
        "morphByManyUpdatedExistingPivot",

        "morphToManyAttached",
        "morphToManyDetached",
        "morphToManySynced",
        "morphToManyToggled",
        "morphToManyUpdatedExistingPivot",
    ],
];
