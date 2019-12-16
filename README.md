# Property System application

This is a basic application used for syncing data from an external api source, and performs a CRUD operation on this set of data.
## Local setup

-   Clone this project in the folder you wish to install in.
-   import the database at the application root folder. property.sql
-   Navigate to project root in command line e.g. cd c:/xampp/htdocs/{project}.
-   `cp` `.env.example` to `.env` and set your environment variables.
-   Run `Composer Insall` .
-   Run `composer start`.


##  Running this application

```bash
composer fetch-data
```

After that, open `http://localhost:7000` in your browser.

###   Given more time, I would have done the following:

-   Paginated the list to reduce load time on the list property page. In my case, I would have used dataTable Ajax requests or simply listed the properties and paginated them using php
-   Gotten the total number of records used to sync data from the API property `total`, rather than defining a static property, since more 
data can be added to the endpoint
-   Properly worked on edit module. I has not been tested (:
-   Improved the overall UI/UX.
-   possibly created a table to keep track of failed syncs.
-   Used phinx package to create database migrates and seeders.
-   worked on dropzone interference with parsley (for form validation)
