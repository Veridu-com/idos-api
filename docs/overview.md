This describes the resources that make up the official Veridu idOS API v1.0.

If you have any problems or requests, please contact support at [support@veridu.com](mailto:support@veridu.com).


## Response Envelopes

All API responses are sent inside an envelopes, separating response metadata from response content more elegantly.

### Success Envelope

#### Paginated Response

Field                   | Type    | Description
------------------------|---------|------------
status                  | boolean | Response status flag (set to `true`)
data                    | array   | Response data, array of objects
pagination.total        | integer | Total items available for current query parameters
pagination.per_page     | integer | Number of items per page
pagination.current_page | integer | Current page index
pagination.last_page    | integer | Last page index
pagination.from         | integer | First item index
pagination.to           | integer | Last item index
updated                 | integer | Unix timestamp for last update on result set (optional field)

#### Single Item Response

Field                   | Type    | Description
------------------------|---------|------------
status                  | boolean | Response status flag (set to `true`)
data                    | object  | Response data, object
updated                 | integer | Unix timestamp for last update on result set (optional field)

### Error Envelope

Field         | Type    | Description
--------------|---------|------------
status        | boolean | Response status flag (set to `false`)
error.code    | integer | Same as the HTTP Status Code
error.type    | string  | The type of error returned
error.link    | string  | Link to additional information about the error
error.message | string  | A descriptive error message


## Schema

All API access is over HTTPS, and accessed from the `api.idos.io` domain.

Request data can be sent as `application/x-www-form-urlencoded` or `application/json`.

Response data can be received as `application/json`, `application/javascript` or `application/xml` depending on the `Accept` header.

For JSON-P (`application/javascript`) responses, a `callback` parameter should be added to the Query String.


## Parameters

All API endpoints will accept one or more of the parameters below, added to the Query String.

Parameter    | Type    | Default | Values                           | Description
-------------|---------|---------|----------------------------------|------------
hideLinks    | boolean | `true`  | `true`, `false`                  | Suppress response's links field
failSilently | boolean | `false` | `true`, `false`                  | Forces a `HTTP 200 OK` response on errors
forceOutput  | string  | `empty` | `json`, `javascript` or `xml`    | Overrides `Accept` header and forces response output format
callback     | string  | `jsonp` | a valid javascript function name | Callback function name for JSON-P responses
forcedError  | string  | `empty` | check endpoint's list of errors  | Forces a response error


## Errors

Veridu idOS API uses conventional HTTP response codes to indicate the success or failure of an API request, however not all errors map cleanly onto HTTP response codes.

```bash
curl -i https://api.idos.io/1.0/profiles/myUser
```
```http
HTTP/1.1 403 Forbidden
Server: nginx
Date: Tue, 22 Dec 2015 19:32:37 GMT
Content-Type: application/json; charset=utf-8
Content-Length: 195
Connection: keep-alive
Cache-Control: no-store,no-cache
X-Content-Type-Options: nosniff
```
```json
{
  "status": false,
  "error": {
    "code": 403,
    "type": "CREDENTIAL_MISSING",
    "link": "https://veridu.com/wiki/CREDENTIAL_MISSING",
    "message": "Credential details missing. Valid Credentials: Token, Private Key"
  }
}
```

## Authentication

There are two ways to authenticate in Veridu idOS API: based on a User Token or on a Credential Token. Requests that require authentication will return `403 Forbidden`. You can manage your API credentials on the [Dashboard](https://dashboard.idos.io).

All tokens are based on [JSON Web Tokens](), a list of recommended libraries to generate and manage tokens can be found [here](https://jwt.io).

### User Token

This should be used by users to interact with the API. Tokens can have a long time span, perfect for mobile integrations.

#### Sent in a Header

This is the preferred method, as it safely transports the token using a HTTP Header.

```bash
curl -H "Authorization: UserToken TOKEN" https://api.idos.io/1.0/
```

#### Sent as Query String

```bash
curl https://api.idos.io/1.0/?userToken=TOKEN
```

### Credential Token

This should be used by handlers to interact with the API.

#### Sent in a Header

This is the preferred method, as it safely transports the token using a HTTP Header.

```bash
curl -H "Authorization: CredentialToken TOKEN" https://api.idos.io/1.0/
```

#### Sent as Query String

```
curl https://api.idos.io/1.0/?token=TOKEN
```

## Rate Limiting

You can check the returned HTTP headers of any API request to see your current rate limit status:

```bash
curl -i https://api.idos.io/1.0/profiles/myUser?privKey=PRIVATE-KEY
```
```http
HTTP/1.1 200 OK
Server: nginx
Date: Tue, 22 Dec 2015 19:03:10 GMT
X-Rate-Limit-Limit: 5400
X-Rate-Limit-Remaining: 5399
X-Rate-Limit-Reset: 1450814590
```

The headers tell you everything you need to know about your current rate limit status:

Header Name            | Description
-----------------------|------------
X-Rate-Limit-Limit     | The maximum number of requests that the consumer is permitted to make per minute.
X-Rate-Limit-Remaining | The number of requests remaining in the current rate limit window.
X-Rate-Limit-Reset     | The time at which the current rate limit window resets in UTC epoch seconds.

Once you go over the rate limit you will receive an error response:

```http
HTTP/1.1 429 Too Many Requests
Server: nginx
Date: Tue, 22 Dec 2015 19:09:23 GMT
X-Rate-Limit-Limit: 5400
X-Rate-Limit-Remaining: 0
X-Rate-Limit-Reset: 1450814590
```

## Conditional Requests

Most responses return an `ETag` header and many also return a `Last-Modified` header. You can use the values of these headers to make subsequent requests to those resources using the `If-None-Match` and `If-Modified-Since` headers, respectively. If the resource has not changed, the server will return a 304 Not Modified.

```bash
curl -i https://api.idos.io/1.0/profiles/myUser?privKey=PRIVATE-KEY
```
```http
HTTP/1.1 200 OK
Server: nginx
Date: Tue, 22 Dec 2015 19:09:31 GMT
Content-Type: application/json; charset=utf-8
Content-Length: 707
Connection: keep-alive
Vary: Accept-Encoding
X-Rate-Limit-Limit: 5400
X-Rate-Limit-Remaining: 5399
X-Rate-Limit-Reset: 1450814971
Last-Modified: Tue, 22 Dec 2015 19:09:18 GMT
X-Content-Type-Options: nosniff
ETag: W/"079f7567b040b8f83e9d246018d7c115cea24c3a"
Cache-Control: private, no-cache, no-store, max-age=0, must-revalidate
```

```bash
curl -i https://api.idos.io/1.0/profiles/myUser?privKey=PRIVATE-KEY -H 'If-None-Match: W/"079f7567b040b8f83e9d246018d7c115cea24c3a"'
```
```http
HTTP/1.1 304 Not Modified
Server: nginx
Date: Tue, 22 Dec 2015 19:10:12 GMT
Connection: keep-alive
X-Rate-Limit-Limit: 5400
X-Rate-Limit-Remaining: 5398
X-Rate-Limit-Reset: 1450814971
Last-Modified: Tue, 22 Dec 2015 19:09:18 GMT
X-Content-Type-Options: nosniff
ETag: W/"079f7567b040b8f83e9d246018d7c115cea24c3a"
Cache-Control: private, no-cache, no-store, max-age=0, must-revalidate
```

```bash
curl -i https://api.idos.io/1.0/profiles/myUser?privKey=PRIVATE-KEY -H 'If-Modified-Since: Tue, 22 Dec 2015 19:09:18 GMT'
```
```http
HTTP/1.1 304 Not Modified
Server: nginx
Date: Tue, 22 Dec 2015 19:10:36 GMT
Connection: keep-alive
X-Rate-Limit-Limit: 5400
X-Rate-Limit-Remaining: 5397
X-Rate-Limit-Reset: 1450814971
Last-Modified: Tue, 22 Dec 2015 19:09:18 GMT
X-Content-Type-Options: nosniff
ETag: W/"079f7567b040b8f83e9d246018d7c115cea24c3a"
Cache-Control: private, no-cache, no-store, max-age=0, must-revalidate
```


## Cross Origin Resource Sharing

The API supports Cross Origin Resource Sharing (CORS) for AJAX requests from any origin.

```bash
curl -i https://api.idos.io/1.0/profiles/myUser?privKey=PRIVATE-KEY -H 'Origin: http://example.com'
```
```http
HTTP/1.1 200 OK
Server: nginx
Date: Tue, 22 Dec 2015 19:25:00 GMT
Content-Type: application/json; charset=utf-8
Content-Length: 707
Connection: keep-alive
Vary: Accept-Encoding
Access-Control-Allow-Origin: http://example.com
Access-Control-Max-Age: 3628800
Access-Control-Allow-Credentials: true
Access-Control-Allow-Methods: GET,DELETE,OPTIONS
Access-Control-Allow-Headers: Authorization, Content-Type, If-Modified-Since, If-None-Match, X-Requested-With
Access-Control-Expose-Headers: ETag, X-Rate-Limit-Limit, X-Rate-Limit-Remaining, X-Rate-Limit-Reset
X-Rate-Limit-Limit: 5400
X-Rate-Limit-Remaining: 5396
X-Rate-Limit-Reset: 1450814971
Last-Modified: Tue, 22 Dec 2015 19:09:18 GMT
X-Content-Type-Options: nosniff
ETag: W/"079f7567b040b8f83e9d246018d7c115cea24c3a"
Cache-Control: private, no-cache, no-store, max-age=0, must-revalidate
```
