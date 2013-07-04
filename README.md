mssql2json
==========

Provide json web service to Microsoft SQL Server 

Usage,

1, update your database connection info
2, upload the file to php-mssql enabled folder
3, examples:

To get all data from table1
http://localhost/mssql2json.php/table1

To get column1 & column2 from table1
http://localhost/mssql2json.php/table1?reqFields=column1,column2

To get data only active from table1
http://localhost/mssql2json.php/table1?active=1

To get data with a custumize where clause
http://localhost/mssql2json.php/table1?whereClause=(column1 ='value1' or column2= true)

All above examples can combine for use. : - )


