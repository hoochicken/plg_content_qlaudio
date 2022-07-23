# plg_content_qlaudio

qlaudio displays a list of audio files and a player

Use following tag: 

~~~html
{qlaudio folder=\"FOLDERNAME\" id=\"CSS_ID\" class=\"CSS_CLASS\"}
~~~

Further params are: 

| #   | name | type   | description                                                                                                                          |
|-----| --- |--------|--------------------------------------------------------------------------------------------------------------------------------------|
| 2   | folderRoot | string | rootfolder, e. g. /images/audio                                                                                                      |
| 1   | folder | string | name of the folder, e. g. 'story_01' => so [folderRoot]/[folder] ist displayed                                                       |
| 3   | limit | int    | number of files to display                                                                                                           |
| 4 | layout | string | 'default', overriding layout of special wishes developped by you                                                                     |
| 4 | id | string | css class                                                                                                                            |
| 4 | class | string | css id                                                                                                                               |
| 4 | autostart | int    | whether autostart activate (1) or not (0)                                                                                            |
| 4 | controls | ?      | frankly speaking: I forgot what it does ...                                                                                          |
| 4 | filesAllowed | string | file endings allowed/displayed                                                                                                       |
| 4 | alterFileName | string | <ul><li>first letter of word capital 'ucwords'</li><li>strip dashed: 'nodash'</li><li>strip file ending: 'stripfileending'</li></ul> |
| 4 | filenameStrip | ?      | whatever |
| 4 | ordering | <ul><li>>'natural'</li><li>'title'</li><li>'' => random</li></ul>|



