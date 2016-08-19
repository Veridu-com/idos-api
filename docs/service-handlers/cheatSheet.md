GET /services

{
    "status": true,
    "data": [
        {
            "id": 11111,
            "name": "Veridu Scraper",
            "events": ["idOS:sourceCreated:facebook", "idOS:sourceCreated:linkedin", "idOS:sourceCreated:twitter"],
            "triggers": [],
            "url": "",
            "created_at": null,
            "updated_at": null
        },
        {
            "id": 222,
            "name": "App Deck LinkedIn Scraper",
            "events": ["idOS:sourceCreated:linkedin"],
            "triggers": [],
            "url": "",
            "created_at": null,
            "updated_at": null
        }
        ...
    ],
    "updated": null
}

GET /services?events=idOS:sourceCreated:facebook

GET /services?name=%scrape%

POST /services

{
    "name": "",
    "listens": [],
    "triggers": [],
    "url": "",
    "username": "",
    "password": ""
}

PUT /services/{id}

{
    "name": "",
    "listens": [],
    "triggers": [],
    "url": "",
    "username": "",
    "password": ""
}

DELETE /services/{id}

GET /handlers

{
    "status": true,
    "data": [
        {
            "id": 999,
            "listens": ["idOS:sourceCreated:facebook", "idOS:sourceCreated:twitter"],
            "service": {
                "id": 11111,
                "name": "Veridu Scraper",
                "events": ["idOS:sourceCreated:facebook", "idOS:sourceCreated:linkedin", "idOS:sourceCreated:twitter"],
                "triggers": [],
                "url": "",
                "created_at": null,
                "updated_at": null
            },
            "created_at": null,
            "updated_at": null
        },
        {
            "id": 888,
            "listens": ["idOS:sourceCreated:linkedin"],
            "service": {
                "id": 222,
                "name": "App Deck LinkedIn Scraper",
                "events": ["idOS:sourceCreated:linkedin"],
                "triggers": [],
                "url": "",
                "created_at": null,
                "updated_at": null
            ],
            "created_at": null,
            "updated_at": null
        }
        ...
    ],
    "updated": null
}

GET /handlers?listens=idOS:sourceCreated:linkedin,idOS:sourceCreated:foursquare

POST /handlers
{
    "service_id": 222,
    "listens": ["idOS:sourceCreated:linkedin"]
}

PUT /handlers/888
{
    "listens": ["idOS:sourceCreated:foursquare"]
}

DELETE /handlers/888