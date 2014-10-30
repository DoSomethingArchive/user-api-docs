# User API Spec

The base URL path for all the endpoints. `1` indicates the version of the API:
```
https://api.dosomething.org/1/
```

## Quick Reference
URL | HTTP Verb | Functionality
--- | --------- | -------------
`/login`                          | POST  | [Logging In](#logging-in)
`/logout`                         | POST  | [Logging Out](#logging-out)
`/users`                          | POST  | [Registering a User](#registering-a-user)
`/users`                          | GET   | [Retrieving a User](#retrieving-a-user)
`/users`                          | PUT   | [Updating a User](#updating-a-user)
`/users/campaigns`                | POST  | [Adding Campaign Info to a User](#adding-campaign-to-user)
`/users/campaigns`                | GET   | [Retrieving a User's Campaigns](#retrieving-users-campaigns)
`/users/campaigns/<campaign_id>`  | PUT   | [Updating User Info for a Single Campaign](#updating-users-campaign)

## Authentication

<h3 id="logging-in">Logging In</h3>

```
POST /login
```

**Parameters:**
In addition to the password, either mobile number or email is required.
```
Content-Type: application/json

{
  /* Required if 'mobile' is not provided */
  email: String,

  /* Required if 'email' is not provided */
  mobile: String,

  /* Required */
  password: String
}
```

**Example Curl:**
```
curl -X POST \
  -H "X-DS-Application-Id: ${APPLICATION_ID}" \
  -H "X-DS-REST-API-Key: ${REST_API_KEY}" \
  -d '{login data}' \
  http://api.dosomething.org/1/login
```

**Example Response:**
TBD: The response could also include the rest of the entirety of the user's document. See [Retrieving a User](#retrieving-a-user) and [Updating a User(#updating-a-user) for the full list of parameters.
```
200 OK
Content-Type: application/json

{
  email: "cooldude6",
  phone: "555-555-5555",
  created_at: "2011-11-07T20:58:34.448Z",
  updated_at: "2011-11-07T20:58:34.448Z",
  doc_id: "g7y9tkhB7O",
  session_token: "pnktnjyb996sj4p156gjtp4im"
}
```

<h3 id="logging-out">Logging Out</h3>

```
POST /logout
```

**Parameters:**
The session token to log out of needs to be provided in the header.

**Example Curl:**
```
curl -X POST \
  -H "X-DS-Application-Id: ${APPLICATION_ID}" \
  -H "X-DS-REST-API-Key: ${REST_API_KEY}" \
  -H "Session: ${SESSION_TOKEN}"
  http://api.dosomething.org/1/logout
```

**Example Response:**
```
200 OK
```

<h3 id="registering-a-user">Registering a User</h3>
Create a new user.

```
POST /users
```

**Open Questions:** Is there a way we can register a new user from a particular application without requiring a password?

**Parameters:**  
In addition to the password, either a mobile number or email is required.
```
Content-Type: application/json

{
  /* Required if 'mobile' is not provided */
  email: String,

  /* Required if 'email' is not provided */
  mobile: String,

  /* Required */
  password: String,

  /* Optional */
  birthdate: Date,
  first_name: String
}
```

**Example Curl:**  
```
curl -X POST \
  -H "X-DS-Application-Id: ${APPLICATION_ID}" \
  -H "X-DS-REST-API-Key: ${REST_API_KEY}" \
  -d '{create data}' \
  http://api.dosomething.org/1/users
```

**Example Response:**
Request fulfilled synchronously.
```
201 Created
Content-Type: application/json

{
  created_at: 2000-01-01T00:00:00Z,
  doc_id: some sort of hash value
}
```

## User Profile

<h3 id="retrieving-a-user">Retrieving a User</h3>
Get profile data for a specific user. This can be retrieved with either a Drupal UID, the database-generated ID, a mobile phone number, or an email address.

```
GET /users?drupal_uid=<drupal_uid>
GET /users?doc_id=<doc_id>
GET /users?mobile=<mobile>
GET /users?email=<email>
```

**Error Responses:**  
`404 Not Found`: The resource does not exist.

**Example Curl:**  
```
curl -X GET \
  -H "X-DS-Application-Id: ${APPLICATION_ID}" \
  -H "X-DS-REST-API-Key: ${REST_API_KEY}" \
  http://api.dosomething.org/1/users?mobile=5555555555
```

**Example Response:**  
This only provides a small example of what a returned user document might look like. For the full list of possible parameters, see [Updating a User](#updating-a-user).
```
200 OK
Content-Type: application/json

{
  email: "test@dosomething.org",
  first_name: First,
  last_name: Last,
  drupal_uid: 123456,
  doc_id: some sort of hash value,
  campaigns: [
    {
      nid: 123,
      rbid: 100,
      sid: 100
    },
    {
      nid: 456,
      sid: 101
    }
  ]
}
```

<h3 id="updating-a-user">Updating a User</h3>
Update a user resource.

```
POST /users?drupal_uid=<drupal_uid>
POST /users?doc_id=<doc_id>
POST /users?mobile=<mobile>
POST /users?email=<email>
```

**Parameters:**  
To update a user resource, a mobile number, email address, Drupal UID, or document ID needs to be provided in the URL.
```
Content-Type: application/json

{
  /* Email address - forced to lowercase */
  email: String,

  /* Mobile phone number */
  mobile: String,

  /* Drupal UID */
  drupal_uid: Number,

  /* Database-generated ID */
  doc_id: String,

  /* Mailing address */
  addr_street1: String,
  addr_street2: String,
  addr_city: String,
  addr_state: String,
  addr_zip: String,

  /* Country */
  country: String,

  /* Date of birth */
  birthdate: Date,

  /* First name */
  first_name: String,

  /* Last name */
  last_name: String,

  /* Timestamps when document was created and last updated */
  created_at: Date,
  updated_at: Date,

  /* List of campaign actions */
  campaigns: Object Array
    [
      {
        /* Campaign node ID */
        nid: Number,

        /* Report back ID */
        rbid: Number,

        /* Sign up ID */
        sid: Number
      },
      ...
    ]
}
```

**Error Responses:**  
`TODO`

**Example Curl:**  
```
curl -X POST \
  -H "X-DS-Application-Id: ${APPLICATION_ID}" \
  -H "X-DS-REST-API-Key: ${REST_API_KEY}" \
  -d {update data} \
  http://api.dosomething.org/1/users?mobile=5555555555
```

**Example Response:**  
Request accepted to be processed asynchronously.
```
202 Accepted
Content-Type: application/json

{
  updated_at: 2000-01-01T00:00:00Z
}
```

<h4 id="adding-campaign-to-user">Adding Campaign Info to a User</h4>
Add campaign info to a user.

```
POST /users/campaigns?drupal_uid=<drupal_uid>
POST /users/campaigns?doc_id=<doc_id>
POST /users/campaigns?mobile=<mobile>
POST /users/campaigns?email=<email>
```

**Parameters:**  
The campaign `nid` is required, and one or both of `report_back` and `sign_up` dates need to be provided.
```
Content-Type: application/json

{
  /* Required. Campaign node ID */
  nid: Number,

  /* Report back ID */
  rbid: Date,

  /* Sign up ID */
  sid: Date
}
```

**Example Curl:**  
```
curl -X POST \
  -H "X-DS-Application-Id: ${APPLICATION_ID}" \
  -H "X-DS-REST-API-Key: ${REST_API_KEY}" \
  -d '{campaign data}' \
  http://api.dosomething.org/1/users/campaigns?mobile=5555555555
```

**Example Response:**
Request fulfilled synchronously.
```
201 Created
Content-Type: application/json

{
  updated_at: 2000-01-01T00:00:00Z,
  doc_id: some sort of hash value
}
```

<h3 id="retrieving-users-campaigns">Retrieving a User's Campaigns</h3>
Get the campaign actions of a specific user. This can be retrieved with either a Drupal UID, the document ID, a mobile phone number, or an email address.

```
GET /users/campaigns?drupal_uid=<drupal_uid>
GET /users/campaigns?doc_id=<doc_id>
GET /users/campaigns?mobile=<mobile>
GET /users/campaigns?email=<email>
```

**Error Responses:**  
`404 Not Found`: The resource does not exist.

**Example Curl:**  
```
curl -X GET \
  -H "X-DS-Application-Id: ${APPLICATION_ID}" \
  -H "X-DS-REST-API-Key: ${REST_API_KEY}" \
  http://api.dosomething.org/1/users/5555555555/campaigns
```

**Example Response:**  
An empty array is returned if no campaign actions have been taken yet:
```
200 OK
Content-Type: application/json

[]
```

If campaign actions do exist, they'll be returned in an array:
```
200 OK
Content-Type: application/json

[
  {
    nid: 123,
    rbid: 100,
    sid: 100
  },
  {
    nid: 456,
    sid: 101
  }
]
```

<h3 id="updating-users-campaign">Updating User Info for a Single Campaign</h3>
Updates the info for a single campaign for a signle user.

```
PUT /users/campaigns?drupal_uid=<drupal_uid>&campaign_id=<campaign_id>
PUT /users/campaigns?doc_id=<doc_id>&campaign_id=<campaign_id>
PUT /users/campaigns?mobile=<mobile>&campaign_id=<campaign_id>
PUT /users/campaigns?email=<email>&campaign_id=<campaign_id>
```

**Parameters:**  
```
Content-Type: application/json

{
  {
    /* Campaign node ID */
    nid: Number,

    /* Date the user signed up for the campaign */
    sign_up: Date,

    /* Date the user last submitted/updated a report back for the campaign */
    report_back: Date
  }
}
```

**Error Responses:**  
`404 Not Found`: The resource does not exist.

**Example Curl:**  
```
curl -X POST \
  -H "X-DS-Application-Id: ${APPLICATION_ID}" \
  -H "X-DS-REST-API-Key: ${REST_API_KEY}" \
  -d {update data} \
  http://api.dosomething.org/1/users/5555555555
```

**Example Response:**  
Request accepted to be processed asynchronously.
```
202 Accepted
```

---

## Dependent Products
#### v1
* Android
  * Querying & updating a user's campaign actions
  * User Profile
  * Registration
  * Login / Logout
* [CGG Voting App](https://github.com/DoSomething/voting-app)
  * User registration
  * User updates

#### v2
* Email/Message Broker
  * Generating campaign digest emails
  * Generating anniversary emails
  * Generating birthday emails

#### Future
* SMS
  * Querying & updating a user's campaign actions
* Web
  * Querying & updating a user's campaign actions
  * User profile
* Data
  * Querying all user information
