# Import Crop Data Tool - User Guide

## Implementation Procedure

### 1. Introduction

#### 1.1 Purpose
The purpose of this document is to provide information on how to prepare files for the **Import Crop Data Tool**.

#### 1.2 System Overview
The Import Crop Data tool is one of the tools from the HUB. This tool has been created with the purpose of importing crop data for future analysis.

##### 1.2.1 System Description
The tool intention is to support crop data importation to the HUB.

### 2. Management Overview
The preparation required will be provided below as a list of steps to follow. The major tasks involved are:
* Saving file as '.csv' type
* Making sure table headers are correct
* Checking file name format

#### 2.1 Points-of-Contact
Role | Name | Contact
---- | ---- | -----
Project/Program Manager | Dr. Jinha Jung | (765) 496-1267
Project Officer | Dr. Anjin Chang | (361) 249-6153
System Developer | Jose Luis Landivar | (817) 704-9757

#### 2.2 Major Tasks

1. Saving file as '.csv' type
    1. If file type is not '.csv':
      1. On Excel: From the top menu bar click on File -> Save As... -> File Format: '.csv'
      2. On Numbers: From the top menu bar click on File -> Export To... -> 'CSV...'

2. Making sure table headers are correct
    1. Verify the file's headers are as follows, written in that order, and start from row 1 column A
      1. row	col	rep	line	grid	entry	name	breeder	irrigation
    2. Verify the file's headers after irrigation have the following format
      1. YearMonthDay For example: 20160407

3. Checking file name format
    1. Verify the file's name is as follows
      1. year_location_crop_type_season_sublocation For example: 2016_cc_cotton_cc_spring_parking

### 3. Implementation Support
This documentation will be provided in addition to the set of files for the HUB system.

#### 3.1 Outstanding Issues
Depending on the file's size, it will take between 2 and 8 minutes to import the data.

#### 3.2 Performance Monitoring
To determine the successful implementation of this tool, proceed to the Crop Analysis page and search for your data by selecting from the options inserted at the moment of importing the file. Click on Search.
If everything was successful, you should your file information on the Data Set table.
