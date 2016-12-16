Veridu idOS is a [RESTful](https://en.wikipedia.org/wiki/Representational_state_transfer) framework for flexible Identity Solutions, i.e. it has resource-oriented URLs and uses HTTP response codes to indicate API errors. It also uses built-in HTTP features, like HTTP authentication and HTTP verbs, which are understood by off-the-shelf HTTP clients.

Support for cross-origin resource sharing is available across all endpoints, allowing you to interact securely with the API from a client-side web application (you should never expose your Private Key in any client-side code, use User Tokens or Credential Tokens instead).

To make the API as explorable as possible, API credentials have development and production modes. There is no "switch" for changing between modes, just use the appropriate credential to perform a live or test request. Requests made with test mode credentials incur no costs.
