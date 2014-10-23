# User API Spec

The base URL path for all the endpoints. `1` indicates the version of the API:
```
https://api.dosomething.org/1/
```

## Quick Reference
URL | HTTP Verb | Functionality
--- | --------- | -------------
`/login`                                   | POST   | [Logging In](#logging-in)
`/logout`                                  | POST   | [Logging Out](#logging-out)
`/users`                                   | GET    | [Retrieving Users](#retrieving-users)
`/users/create`                            | POST   | [Registering a User](#registering-a-user)
`/users/<user_id>`                         | GET    | [Retrieving a User](#retrieving-a-user)
`/users/<user_id>`                         | POST   | [Updating a User](#updating-a-user)
`/users/<user_id>/campaigns`               | GET    | [Retrieving a User's Campaigns](#retrieving-users-campaigns)
`/users/<user_id>/campaigns`               | POST   | [Updating Info for all of a User's Campaigns](#updating-users-campaigns)
`/users/<user_id>/campaigns/<campaign_id>` | POST   | [Updating User Info for a Single Campaign](#updating-users-campaign)

## Authentication

<h3 id="logging-in">Logging In</h3>

```
POST /login
```

<h3 id="logging-out">Logging Out</h3>

```
POST /logout
```

<h3 id="registering-a-user">Registering a User</h3>
Create a new user.

```
POST /users/create
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
`TODO`

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
GET /users/<drupal_uid>
GET /users/<doc_id>
GET /users/<mobile>
GET /users/<email>
```

**Error Responses:**  
`404 Not Found`: The resource does not exist.

**Example Curl:**  
`TODO`

**Example Response:**  
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
POST /users/<drupal_uid>
POST /users/<doc_id>
POST /users/<mobile>
POST /users/<email>
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
`TODO`

**Example Response:**  
Request accepted to be processed asynchronously.
```
202 Accepted
Content-Type: application/json

{
    updated_at: 2000-01-01T00:00:00Z
}
```

<h3 id="retrieving-users-campaigns">Retrieving a User's Campaigns</h3>
Get the campaign actions of a specific user. This can be retrieved with either a Drupal UID, the document ID, a mobile phone number, or an email address.

```
GET /users/<drupal_uid>/campaigns
GET /users/<doc_id>/campaigns
GET /users/<mobile>/campaigns
GET /users/<email>/campaigns
```

**Error Responses:**  
`404 Not Found`: The resource does not exist.

**Example Curl:**  
`TODO`

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

<h3 id="updating-users-campaigns">Updating Info for all of a user's Campaign</h3>
Update a user's campaign actions.

```
POST /users/<drupal_uid>/campaigns
POST /users/<doc_id>/campaigns
POST /users/<mobile>/campaigns
POST /users/<email>/campaigns
```

**Parameters:**  
```
Content-Type: application/json

{
    /* List of campaign actions */
    campaigns: Object Array
        [
            {
                /* Campaign node ID */
                nid: Number,

                /* Date the user signed up for the campaign */
                sign_up: Date,

                /* Date the user last submitted/updated a report back for the campaign */
                report_back: Date
            },
            ...
        ]
}
```

**Error Responses:**  
`404 Not Found`: The resource does not exist.

**Example Curl:**  
`TODO`

**Example Response:**  
Request accepted to be processed asynchronously.
```
202 Accepted
```

<h3 id="updating-users-campaign">Updating User Info for a Signle Campaign</h3>

## Bulk Querying
For systems like the digest e-mail creation, we can offer ways to query for a group of users.

<h3 id="retrieving-users">Retrieving Users</h3>
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
`TODO`

#### Users by Anniversary Date
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

#### Users by Birthdate
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

#### Users with Campaign Actions
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
