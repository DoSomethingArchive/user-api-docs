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
Either mobile number or email is required.
```
Content-Type: application/json

{
    /* Required if 'mobile' is not provided */
    email: String,

    /* Required if 'email' is not provided */
    mobile: String
}
```

**Example Curl:**
```
curl -X POST \
  -H "X-DS-Application-Id: ${APPLICATION_ID}" \
  -H "X-DS-REST-API-Key: ${REST_API_KEY}" \
  -d '{logout data}' \
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
            report_back: "2014-04-10T00:00:00Z",
            sign_up: "2014-04-08T00:00:00Z"
        },
        {
            nid: 456,
            sign_up: "2014-05-01T00:00:00Z"
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

    /* Date of birth */
    birthdate: Date,

    /* Date when Drupal account was created */
    drupal_register_date: Date

    /* Email subscription status. ex: subscribed, unsbuscribed, etc. */
    email_status: Number,

    /* First name */
    first_name: String,

    /* Last name */
    last_name: String,

    /* SMS subscription status */
    mobile_status: Number,
    
    /* User roles - for determining access rights. Copying from Drupal user DB for now. */
    roles: [
        1, /* authenticated user */
        3, /* administrator */
        4, /* editor */
        6, /* communications team */
        7, /* member support */
        
        /* POTENTIAL FUTURE ROLES */
        8, /* 3rd party developer - particularly for hackathons and such */
    ],

    /* GreatSchools ID of the user's school */
    school_gsid: Number,

    /* Name of the user's school */
    school_name: String,

    /* List of campaign actions */
    campaigns: Object Array
        [
            {
                nid: Number,
                sign_up: Date,
                report_back: Date
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

  /* Campaign report back date */
  report_back: Date,

  /* Campaign sign update date */
  sign_up: Date
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
        report_back: "2014-04-10T00:00:00Z",
        sign_up: "2014-04-08T00:00:00Z"
    },
    {
        nid: 456,
        sign_up: "2014-05-01T00:00:00Z"
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

## Bulk Querying
We can offer ways to query for a group of users. This is currently driven largely by the needs of our digest e-mail creation system.

URL | HTTP Verb | Functionality
--- | --------- | -------------
`/users`                      | GET | [Retrieving Users](#retrieving-users)
`/users?anniversary_date`     | GET | [Retrieve by Anniversary Date](#retrieve-by-anniversary-date)
`/users?birthdate`            | GET | [Retrieve by Birthdate](#retrieve-by-birthdate)
`/users?exclude_no_campaigns` | GET | [Retrieve if Signed Up for any Campaign](#retrieve-by-campaign-action)

<h4 id="retrieving-users">Retrieving Users</h3>
Get all users. By default, results will be paginated only returning a subset of all users.

```
GET /users
```

**Additional Parameters:**  
`page=<page_num>`: For pagination. Specifies the page number to skip to. _Default: 1_

`page_size=<page_size>`: For pagination. Requires the `page` param to be set. Sets the page size. _Default: 100_

**Successful Responses:**  
`200 OK`: Request succeeded. Full user documents will be returned in an array. A query with no results will return an empty array.

**Example Curl:**  
```
curl -X GET \
  -H "X-DS-Application-Id: ${APPLICATION_ID}" \
  -H "X-DS-REST-API-Key: ${REST_API_KEY}" \
  http://api.dosomething.org/1/users
```

<h4 id="retrieve-by-anniversary-date">Users by Anniversary Date</h4>
Get all users who have an anniversary with subscribing to DoSomething.org. If a year is not specified, then it will return all users who have an anniversary on that day across all years.

**Query:**  
```
GET /users?anniversary_date=<m-d-Y>
GET /users?anniversary_date=<m-d>
```

**Additional Parameters:**  
`page=<page_num>`: For pagination. Specifies the page number to skip to. _Default: 1_

`page_size=<page_size>`: For pagination. Requires the `page` param to be set. Sets the page size. _Default: 100_

**Successful Responses:**  
`200 OK`: Request succeeded. Full user documents will be returned in an array. A query with no results will return an empty array.

**Example Curl:**  
`TODO`

<h4 id="retrieve-by-birthdate">Users by Birthdate</h4>
Get all users who have a birthday on a given day. If a year is not specified, then it will return all users who have a birthday on that day across all years.

**Query:**  
```
GET /users?birthdate=<m-d-Y>
GET /users?birthdate=<m-d>
```

**Additional Parameters:**  
`page=<page_num>`: For pagination. Specifies the page number to skip to. _Default: 1_

`page_size=<page_size>`: For pagination. Requires the `page` param to be set. Sets the page size. _Default: 100_

**Successful Responses:**  
`200 OK`: Request succeeded. Full user documents will be returned in an array. A query with no results will return an empty array.

**Example Curl:**  
`TODO`

<h4 id="retrieve-by-campaign-action">Users with Campaign Actions</h4>
Get all users who have taken a campaign action.

**Query:**  
```
GET /users?exclude_no_campaigns=1
```

**Additional Parameters:**  
`page=<page_num>`: For pagination. Specifies the page number to skip to. _Default: 1_

`page_size=<page_size>`: For pagination. Requires the `page` param to be set. Sets the page size. _Default: 100_

**Successful Responses:**  
`200 OK`: Request succeeded. Full user documents will be returned in an array. A query with no results will return an empty array.

**Example Curl:**  
`TODO`

## Other Status Codes
`400 Bad Request`: Invalid usage of a resource.

`401 Unauthorized`: Request is not authenticated. API token is missing or invalid.

`403 Forbidden`: The request is not authorized. Credentials do not provide access to the resource.

`406 Not Acceptable`: The requested resource is only capable of generating content not acceptable according to the Accept headers sent in the request. Currently only planning on supporting application/json.

`422 Unprocessable Entity`: Invalid parameters provided in a request.

---

## Dependent Products
* Android
    * Querying & updating a user's campaign actions
    * User Profile
    * Registration
    * Login / Logout
* SMS
    * Querying & updating a user's campaign actions
* Email/Message Broker
    * Generating campaign digest emails
    * Generating anniversary emails
    * Generating birthday emails
* Web
    * Querying & updating a user's campaign actions
    * User profile
