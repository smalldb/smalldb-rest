{
    "_": "<?php printf('_%c%c}%c',34,10,10);__halt_compiler();?>",
    "flupdo": {
        "driver": "sqlite",
        "host": null,
        "port": null,
        "database": "./data/database.sqlite",
        "username": null,
        "password": null,
        "log_query": null,
        "log_explain": null
    },
    "smalldb": {
        "base_dir": "./statemachine",
        "cache_disabled": true
    },
    "auth": {
        "class": "Smalldb\\StateMachine\\Auth\\AllowAllAuth"
    }
}
