# B1RTHDAY H1TS
Get all the number one songs on each of your birthdays

This was meant as a project/challenge/exercise - so, there is room for improvement :-)

By typing in your birthday, you will get all the number one songs of each year, that were the current number one on that date.

### My Workflow:

* Database (design and create) for all the information (Datasource: Wikipedia).
* Collect all number one songs from germany. Fill the database.
* PHP to get the Information from the Database.
* Make it look nice with CSS.

## Known issues (not sorted)
* Birthdays before 1954 (before Germany had charts): The year and birthday is off
* Birthdays in the beginning of the year: The database call is selecting the closest earlist date but doesn't get late december charts in  the year before
* Maintenance: Still some UTF-8 problems in the database-source
* Maintenance: Find better song versions on Spotify (caused by difference between source and Spotify API in title and/or artist)
* Minor CSS issue: align title (caused by cover size difference)
* Minor CSS issue: algin artist (caused by short/long songtitle)
* Not perfectly adjusted for a device with a smaller screen.

## To-Do (not sorted)
* Create output as a playlist on Spotify
* Share this page and output on social media
* Add US / UK number one songs (and also other countries)
* Add amazon affiliate links ;-)
* Provide other searches (i.e. by year, by artist)
* Add Footer
