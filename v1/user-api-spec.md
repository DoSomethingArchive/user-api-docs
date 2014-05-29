# User API Spec

The base URL path for all the endpoints that follow should be something along the lines of:
```
https://api.dosomething.org/v1
```

## Authentication
User creation:
`POST /user/register` or can we/should we just use `POST /user`?

User login/logouts. If we go with Drupal-like sessions, I imagine we'll need `POST /user/login` and `POST /user/logout`. But are there different and better ways to go here? OAuth2? Something else?

## User Profile

### User GET
Get profile data for a specific user. This can be retrieved with either a Drupal UID, a Mongo UID, a mobile phone number, or an email address.

**Endpoints:**  
```
/user/<:drupal_uid>
/user/<:mongo_uid>
/user/<:mobile>
/user/<:email>
```

**Request Method:**  
`GET`

**Successful Responses:**  
`200 OK`: Request succeeded.

**Error Responses:**  
`404 Not Found`: The resource does not exist.

**Example Curl:**  
TODO

**Example Response:**  
```
200 OK

{
    email: "test@dosomething.org",
    first_name: First,
    last_name: Last,
    drupal_uid: 123456,
    mongo_uid: some sort of hash value,
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

### User POST
Create and/or update a user resource.

**Endpoint:**  
```
/user
```

**Request Method:**  
`POST`

**Parameters:**  
Either a mobile number or email address needs to be provided in order to create a user. To update a user resource, a mobile number, email address, Drupal UID, or Mongo UID can be used.

Content-Type: application/json
```
{
    /* Email address */
    email: String,

    /* Mobile phone number */
    mobile: String,

    /* Drupal UID */
    drupal_uid: Number,

    /* Mongo-assigned ID */
    mongo_uid: String,

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

**Successful Responses:**  
`201 Created`: When creating a user, request fulfilled synchronously.  
`202 Accepted`: When updating a user, request accepted to be processed asynchronously.

**Error Responses:**  

**Example Curl:**  
TODO

**Example Response:**  
For successful requests that create a document, a 201 status code will be returned with a copy of the contents of the resource created.
```
201 Created

{
    email: "test@dosomething.org",
    first_name: First,
    last_name: Last,
    birthdate: 2000-01-01T00:00:00Z,
    email_status: 1
}
```

### User Campaigns GET
Get the campaign actions of a specific user. This can be retrieved with either a Drupal UID, a Mongo UID, a mobile phone number, or an email address.

**Endpoints:**  
```
/user/<:drupal_uid>/campaigns
/user/<:mongo_uid>/campaigns
/user/<:mobile>/campaigns
/user/<:email>/campaigns
```

**Request Method:**  
`GET`

**Successful Responses:**  
`200 OK`: Request succeeded.

**Error Responses:**  
`404 Not Found`: The resource does not exist.

**Example Curl:**  
TODO

**Example Response:**  
An empty array is returned if no campaign actions have been taken yet:
```
200 OK

[]
```

If campaign actions do exist, they'll be returned in an array:
```
200 OK

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

### User Campaigns POST
Update a user's campaign actions.

**Endpoints:**  
```
/user/<:drupal_uid>/campaigns
/user/<:mongo_uid>/campaigns
/user/<:mobile>/campaigns
/user/<:email>/campaigns
```

**Request Method:**  
`POST`

**Parameters:**  
Content-Type: application/json
```
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

**Successful Responses:**  
`202 Accepted`: Request accepted to be processed asynchronously.

**Error Responses:**  
`404 Not Found`: The resource does not exist.

**Example Curl:**  
TODO

**Example Response:**  
```
202 Accepted
```

## Bulk Querying
For systems like the digest e-mail creation, we can offer ways to query for a group of users.

### All Users
Get all users. By default, results will be paginated only returning a subset of all users.

**Endpoint:**  
```
/users
```

**Request Method:**  
`GET`

**Additional Parameters:**  
`page=<:page_num>`: For pagination. Specifies the page number to skip to. _Default: 1_

`page_size=<:page_size>:` For pagination. Requires the `page` param to be set. Sets the page size. _Default: 100_

**Successful Responses:**  
`200 OK`: Request succeeded. Full user documents will be returned in an array. A query with no results will return an empty array.

**Example Curl:**  
TODO

#### Users by Anniversary Date
Get all users who have an anniversary with subscribing to DoSomething.org.

**Query:**  
```
/users?anniversary_date=<:m-d-Y>
```

**Request Method:**  
`GET`

**Additional Parameters:**  
`page=<:page_num>`: For pagination. Specifies the page number to skip to. _Default: 1_

`page_size=<:page_size>:` For pagination. Requires the `page` param to be set. Sets the page size. _Default: 100_

**Successful Responses:**  
`200 OK`: Request succeeded. Full user documents will be returned in an array. A query with no results will return an empty array.

**Example Curl:**  
TODO

#### Users by Birthdate
Get all users who have a birthday on a given day.

**Query:**  
```
/users?birthdate=<:m-d-Y>
```

**Request Method:**  
`GET`

**Additional Parameters:**  
`page=<:page_num>`: For pagination. Specifies the page number to skip to. _Default: 1_

`page_size=<:page_size>:` For pagination. Requires the `page` param to be set. Sets the page size. _Default: 100_

**Successful Responses:**  
`200 OK`: Request succeeded. Full user documents will be returned in an array. A query with no results will return an empty array.

**Example Curl:**  
TODO

#### Users with Campaign Actions
Get all users who have taken a campaign action.

**Query:**  
```
/users?exclude_no_campaigns=1
```

**Request Method:**  
`GET`

**Additional Parameters:**  
`page=<:page_num>`: For pagination. Specifies the page number to skip to. _Default: 1_

`page_size=<:page_size>:` For pagination. Requires the `page` param to be set. Sets the page size. _Default: 100_

**Successful Responses:**  
`200 OK`: Request succeeded. Full user documents will be returned in an array. A query with no results will return an empty array.

**Example Curl:**  
TODO

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
* Email
    * Generating campaign digest emails
    * Generating anniversary emails
    * Generating birthday emails
* Web
    * Querying & updating a user's campaign actions
    * User profile
