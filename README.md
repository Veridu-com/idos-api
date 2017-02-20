```c

        88888                                                      88888
        88        88           88    ,ad8888ba,     ad88888ba         88
        88        ""           88   d8"'    `"8b   d8"     "8b        88
        88                     88  d8'        `8b  Y8,                88
        88        88   ,adPPYb,88  88          88  `Y8aaaaa,          88
        88        88  a8"    `Y88  88          88    `"""""8b,        88
        88        88  8b       88  Y8,        ,8P          `8b        88
        88        88  "8a,   ,d88   Y8a.    .a8P   Y8a     a8P        88
        88        88   `"8bbdP"Y8    `"Y8888Y"'     "Y88888P"         88
        88888                                                      88888

```

This is the code repository for idOS API.

# Setup

You can read how to setup the idOS API in the [Setup Manual](Setup.md)

# Operation

You can read how to operate the idOS API in the [Operation Manual](Operation.md)

# Extending

You can read how to extend the idOS API in the [Extension Manual](Extend.md)

# Documentation

To generate the documentation, use [apiDoc](https://bitbucket.org/flavioheleno/apidoc).

```
#!bash
./app/apiDoc.php doc:gen --blueprint -- /path/to/idos-api/app/ /path/to/idos-api/ "idOS - A flexible framework for identity solutions." /path/to/idos-api/docs/description.md "https://api.idos.io" "/1.0/" /path/to/idos-api/docs/overview.md
```
