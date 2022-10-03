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
        "deleted"
    ],
];
