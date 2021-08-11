CREATE TABLE tx_verowa_event (
    event_id int(11) unsigned NOT NULL,
    date_from int(11) unsigned NOT NULL,
    date_to int(11) unsigned NOT NULL,
    hide_time tinyint(1) unsigned NOT NULL DEFAULT 0,
    date_text varchar(255) NOT NULL DEFAULT '',
    title varchar(255) NOT NULL DEFAULT '',
    topic varchar(255) NOT NULL DEFAULT '',
    short_desc varchar(1024) NOT NULL DEFAULT '',
    long_desc mediumtext,
    organizer int(11) unsigned COMMENT '1:n => person.id',
    coorganizers int(11) unsigned NOT NULL DEFAULT 0 COMMENT 'coorganizers (persons) count',
    further_coorganizers varchar(1024) NOT NULL DEFAULT '',
    lectors int(11) unsigned NOT NULL DEFAULT 0 COMMENT 'lectors (persons) count',
    visitators int(11) unsigned NOT NULL DEFAULT 0 COMMENT 'visitators (persons) count',
    organists int(11) unsigned NOT NULL DEFAULT 0 COMMENT 'organists (persons) count',
    vergers int(11) unsigned NOT NULL DEFAULT 0 COMMENT 'vergers (persons) count',
    catering varchar(255) NOT NULL DEFAULT '',
    with_sacrament tinyint(1) unsigned NOT NULL DEFAULT 0,
    childcare_id tinyint(1) unsigned NOT NULL DEFAULT 0,
    childcare_text varchar(255) NOT NULL DEFAULT '',
    childcare_person int(11) unsigned COMMENT '1:n => person.id',
    subscribe_date varchar(255),
    subscribe_person int(11) unsigned COMMENT '1:n => person.id',
    baptism_offer_id tinyint(1) unsigned NOT NULL DEFAULT 0,
    baptism_offer_text varchar(255) NOT NULL DEFAULT '',
    collection int(11) unsigned COMMENT '1:n => collection.id',
    target_groups int(11) unsigned NOT NULL DEFAULT 0 COMMENT 'target_groups (target_groups) count',
    layers int(11) unsigned NOT NULL DEFAULT 0 COMMENT 'layers (layers) count',
    rooms int(11) unsigned NOT NULL DEFAULT 0 COMMENT 'rooms (rooms) count',
    files int(11) unsigned NOT NULL DEFAULT 0 COMMENT 'files (files) count',
    image_url varchar(1024) NOT NULL DEFAULT '',
    image_width int(11) unsigned NOT NULL DEFAULT 0,
    image_height int(11) unsigned NOT NULL DEFAULT 0,

    UNIQUE event_id (event_id),
);

CREATE TABLE tx_verowa_room (
    room_id int(11) unsigned NOT NULL,
    room_name varchar(255) NOT NULL DEFAULT '',
    shortcut varchar(40) NOT NULL DEFAULT '',
    location_id int(11) unsigned COMMENT '1:n => location.id',
    location_name varchar(255) NOT NULL DEFAULT '',
    street varchar(255) NOT NULL DEFAULT '',
    postcode varchar(255) NOT NULL DEFAULT '',
    city varchar(255) NOT NULL DEFAULT '',
    location_url varchar(1024),
    location_url_is_external tinyint(1) unsigned NOT NULL DEFAULT 0,

    UNIQUE room_id (room_id),
);

CREATE TABLE tx_verowa_event_room_mm (
    uid_local int(11) unsigned DEFAULT 0 NOT NULL COMMENT 'tx_verowa_event.uid',
    uid_foreign int(11) unsigned DEFAULT 0 NOT NULL COMMENT 'tx_verowa_room.uid',
    sorting int(11) DEFAULT '0' NOT NULL,

--    PRIMARY KEY (uid_local,uid_foreign),
    KEY uid_local (uid_local),
    KEY uid_foreign (uid_foreign)
);
