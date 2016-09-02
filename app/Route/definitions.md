# Definitions

## URI Fragments

Always use the proper definition for URI Fragments.

If you are creating a fragment that is not listed below, create a new rule.

Fragment  | Regular Expression    | Usage example
----------|-----------------------|--------------
userName  | `[a-zA-Z0-9_-]+`      | `{userName:[a-zA-Z0-9_-]+}`
*Slug     | `[a-z0-9-]+`          | `{companySlug:[a-z0-9-]+}`
*Id       | `[0-9]+`              | `{sourceId:[0-9]+}`
routeName | `[a-zA-Z]+:[a-zA-Z]+` | `{routeName:[a-zA-Z]+:[a-zA-Z]+}`
*Name     | `[a-zA-Z0-9]+`        | `{attributeName:[a-zA-Z0-9]+}`
