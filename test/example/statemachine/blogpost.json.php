{
    "class": "Smalldb\\StateMachine\\FlupdoCrudMachine",
    "table": "blogpost",
    "desc": "Blog post from paper",
    "state_select": "(CASE WHEN blogpost.isDeleted THEN 'deleted' WHEN blogpost.publishTime IS NOT NULL THEN 'published' ELSE 'writing' END)",
    "no_default_machine": true,
    "states": {
        "writing": {
        },
        "published": {
        },
        "deleted": {
        }
    },
    "actions": {
        "create": {
            "returns": "new_id",
            "transitions": {
                "": {
                    "targets": [ "writing" ]
                }
            }
        },
        "edit": {
            "transitions": {
                "writing": {
                    "targets": [ "writing" ]
                },
                "published": {
                    "targets": [ "published" ]
                }
            }
        },
        "publish": {
            "transitions": {
                "writing": {
                    "targets": [ "published", "writing" ]
                }
            }
        },
        "hide": {
            "transitions": {
                "published": {
                    "targets": [ "writing" ]
                }
            }
        },
        "delete": {
            "transitions": {
                "writing": {
                    "targets": [ "deleted" ]
                },
                "published": {
                    "targets": [ "deleted" ]
                }
            }
        },
        "undelete": {
            "transitions": {
                "deleted": {
                    "targets": [ "writing", "published" ]
                }
            }
        }
    },
    "properties": {
        "id": {
            "type": "number",
            "label": "ID",
            "is_pk": true,
            "link": "/article/{id}",
            "calculated": true
        },
        "title": {
            "type": "text",
            "label": "Title",
            "required": true,
            "link": "/article/{id}"
        },
        "publishTime": {
            "type": "datetime",
            "label": "Published",
            "format": "%Y-%m-%d %H:%M"
        },
        "isDeleted": {
            "type": "check",
            "label": "Deleted"
        }
    }
}

