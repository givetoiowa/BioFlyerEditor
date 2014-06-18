#BioFlyer Editor 
A WordPress plugin that allows the user to search, edit, create, and delete Bioflyers. 

The file bde.php makes up the WP interface/dashboard. The functionality of the plugin is done with ajax calls. The script bde-js.js has all of the plugin js/ajax stuff. All ajax references bde-process.php. 

The BioFlyers class in bde-BioFlyers.php has all of the logic for making queries on the database. The class itself extends to BF_Database which has methods for making prepared queries. If a method in BioFlyers does not get the information you want write a new method in the BioFlyers class instead of trying to access the BF_Database class directly 

##Database Structure
There are two tables bioflyers and bf areas.

Structure of bioflyers

Field      |   Type       | Null | Key  | Default |     Extra      |
-----------|--------------|------|------|---------|----------------|
id         | int(11)      |  NO  | PRI  | NULL    | auto_increment |
area_id    | int(11)      |  NO	 | MUL  | NULL    |                |	 
title	     | varchar(150) |	 YES | NULL	|         |                | 
body	     | text	        |  YES | NULL	|         |                | 
file_under | varchar(1) 	|  NO	 | NULL	|         |                |

Structure of bf_areas

Field   |   Type       | Null | Key  | Default |     Extra      |
--------|--------------|------|------|---------|----------------|
id      | int(11)      |  NO  | PRI  | NULL    | auto_increment |
title   | varchar(150) |  Yes |      | NULL    |                |
dir_url | varchar(150) |  Yes |      | NULL    |                | 
