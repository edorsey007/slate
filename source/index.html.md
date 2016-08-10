---
title: LiveAudience API

language_tabs:
  - Code Examples

toc_footers:
  - <a href='#'>Sign Up for a Developer Key</a>

search: true
---

# Introduction

LiveIntent supports the use of partner-provided 1st-party data for use in targeting ad campaigns. This document describes the process whereby partners can supply 1st-party data files and segmentation information to LiveIntent for subsequent targeting in either programmatic transactions or trafficking campaigns directly in the LiveIntent UI.  

# Change History

|     Date     | Responsible | Change                                                                                                                        |
|------------|-----------|-------------------------------------------------------------------------------------------------------------------------------|
| 11/21/2014 | Dave Wright | Created v1.1:                                                                                                                 |
|              |             | Added use case of using API to upload Audience data into LiveIntent API.                                                    |
|              |             | Modified protocol to use sftp only for transfer of email hashes, metadata containing segment ids, and acknowledgement.      |
| 12/12/2014 | Dave Wright | Created v1.2:                                                                                                                 |
|              |             | Sub-id is required for agencies, optional for others.                                                                       |
|              |             | Address errors in initial metadata upload by putting metadata validation response into sftp directory. Updated error codes. |
|              |             | Provide naming conventions for files in sftp.                                                                              |
| 08/09/2016   | Kyle Brown | Created v1.3: |
|              |            | Minor updates to reflect current implementation. |


# Overview 

## Basic Workflow

This basic workflow for placing 1st-party data files into service on the LiveIntent platform is:

1. Partner receives data file from agency, ATD, advertiser, or publisher. This file contains MD5, SHA1 or SHA2 hashes of email addresses to be targeted.

2. Partner creates segment metadata file describing segmentation identifiers for the email addresses.

3. Partner and LiveIntent coordinate SFTP endpoint and SSH public key for authentication checks.

4. Data files of email hashes `<filename>` and metadata describing segments for the email addresses `<filename>.json` are uploaded via SFTP to LiveIntent. LiveIntent validates the metadata and provides a validation summary `<filename>.status` in the SFTP directory.

5. Using the supplied metadata, the LiveIntent platform processes the hash file.

6. Upon completion of processing the file, a `<filename>.status` file containing this success/failure status will be place in the LiveIntent SFTP directory.

7. Segment data is made available for targeting on LiveIntent inventory: 

  1. [**LiveIntent UI Users**](#liveintent-ui-users): Segments may be managed and targeted to ad campaigns within the LiveIntent UI.

  2. [**Programmatic Bidders**](#programmatic-transactions): As appropriate, LiveIntent supplies segment data to partner during subsequent programmatic transactions.

## SFTP File Transfers

LiveIntent will supply each partner with information regarding the secure uploading of data files to a LiveIntent server, including destination and credentials. Please consult with your LiveIntent integration contact for details.

* The metadata file should have a .json extension: `<filename>.json`.

* Once LiveIntent processes the metadata file, it will create a
`<filename>.status` file for errors in the metadata; this response should be
available in the SFTP folder within 30 minutes.

* Once the first party file has been processed, LiveIntent will create a `<filename>.status` file, indicating processed status; this response might take up to 24 hours.


<aside class="notice">
The **_\<filename\>_** should be consistent for all files, e.g. if the partner uploads **_my_request.json**, LiveIntent will create **_my_request.status**. The metadata file should have the same name as the data file as a best practice, but is not mandatory.
</aside>

## File Formats

### Email hash data file

The 1st-party email hash data file should be in CSV format and consist of a single column of _entirely_ MD5, SHA1 or SHA2 hashed email addresses. (Important: Email addresses must be in lowercase format prior to hashing; failure to adhere to this specification will result in user-targeting failure.) Files may be compressed in either .GZ or .ZIP format for efficiency.

### Segmentation metadata file

The 1st party segmentation metadata file contains a description of partner owned segments to be added or removed. If a specified segment does not exist, it will be created.

The content of the message body must be JSON and adhere to the following structure. All fields are _required_ unless indicated otherwise.

The `file` object contains metadata about the data file in order to verify that the processing is to be applied to a specific email hash data file.

The `segments` array accepts multiple `segment` objects, each with
an `action` parameter. This way the partner can modify one or more segments in a single call, applying the data file as either an addition or removal in each case.

# Request Body


```


{
  "partner_id" : "c9eefe7ba1861a601d01a0f3e8f25573", 
  "sub_id" : "142da56bcfbc547dc206e0952edb6214", 
  "file" : {
    "name" : "435ed7e9f07f740abf511a62c00eef6e.txt>",
  "hash" : "md5",
  "digest" : "435ed7e9f07f740abf511a62c00eef6e", "date" : "2014-12-12",
  "records" : 1000000
  },
  "segments" : [ 
  {
  "id" : "123", 
  "name" : "Recent Purchasers", 
  "action" : "add"
  },
  {
  "id" : "456", 
  "name" : "Inactive Subscribers", 
  "action" : "remove"
  }
 ]
}
```

| Property                         | Description                                                                                                                                                                                                                                       | Type             |
|----------------------------------|---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|------------------|
| partner_id                       | Partner ID (assigned by LiveIntent)                                                                                                                                                                                                               | string           |
| sub_id           | Sub ID (assigned by LiveIntent). This field may be used by UI Partners to specify a partner-managed advertiser.                                                        | string           |
| file                             | Data file metadata                                                                                                                                                                                                                                | object           |
| name                             | Name of file as _\<digest_value\>.txt_                                                                                                                                                                                                               | string           |
| hash                             | Designates the hashing algorithm used to derive the digest of the file.                                                                                                                                                                           | "md5" or "sha1"     |
| digest                           | Calculated message digest of the file based on hash                                                                                                                                                                                                           | string           |
| date                             | Date of file upload                                                                                                                                                                                                                               | yyyy-mm-dd       |
| records                          | Number of records in file                                                                                                                                                                                                                         | integer          |
| segments                         | Array of segment objects                                                                                                                                                                                                                          | array of objects |
| id                               | Partner defined segment ID.  Must be integer values only. Other values will be rejected and the segment action will fail.  It is up to the partner to ensure that there is no possibility of collisions across segment IDs within their platform. | string           |
| name                             | Partner segment name                                                                                                                                                                                                                              | string           |
| action                           | Indicates whether hashes in the data file should be added to or removed from segment                                                                                                                                                                                 | "add" or "remove"   |

# Processing Status

After attempting to process the data file as directed by the metadata, LiveIntent will provide status to the partner acknowledging the attempt and indicating the result of the operation. This status will be provided as `<filename>.status` in the SFTP folder. It is the responsibility of the partner to retrieve and process the status file and take further action if necessary. 

The content of the status file will be JSON format. In the event of a successful operation, the file will contain the file metadata and segment information as well as a LiveIntent generated `transaction_id`. The structure of the file will be as follows: 

```
{
  "transaction_id" : "12345678",
  "partner_id" : "1001", 
  "sub_id" : "476378", 
  "file" : {
    "name" : "435ed7e9f07f740abf511a62c00eef6e.txt>",
    "hash" : "md5",
    "digest" : "435ed7e9f07f740abf511a62c00eef6e", 
    "date" : "2014-12-12",
    "records" : 1000000
  },
  "segments" : [ 
  {
    "id" : "123",
    "action" : "add",
    "code" : "code",
    "records" : 1000000
  },
  {
    "id" : "456", 
    "action" : "remove",
    "code" : "code",
    "records" : 1000000 
  }
 ]
}


```


# Errors
```
{
"error" : { 
  "code" : " 8", 
  "description" : "File has no valid records"
 } 
} 

```


In the event of a failed operation, an error message will be returned.

| Code | Description                        |
|------|------------------------------------|
| 0    | Internal error                     |
| 1    | Request no well formed             |
| 2    | Partner id invalid or missing      |
| 3    | Sub id invalid or missing          |
| 4    | Filename contains slash characters |
| 5    | Callback URL wrong format          |
| 6    | File not found                     |
| 7    | File checksum validation failed    |
| 8    | File has no valid records          |

# Accessing Segment Data
LiveIntent allows partners to target their audiences directly in the LiveIntent UI or via programmatic bidding.

## Programmatic Transactions


```
{
  "data": [
    {
      "id": "1",
      "name": "Partner",
      "segment": [ 
        {"id": "123"},
        {"id": "456"}
      ]
    }
  ]
}

```
LiveIntent will communicate segment data to the partner within the bid request object, as appropriate, using existing mechanisms in the [OpenRTB specification](http://www.iab.net/media/file/OpenRTBAPISpecificationVersion2_2.pdf). We support the following attributes in the `data` object child of the `user` object for this purpose: 

Data object:

| Property | Description | Type |
|----------|-------------|------|
| id | Partner ID assigned by LiveIntent | string |
| name | Partner name | string |
| segment | Array of segment objects | array |

Segment object:

| Property | Description | Type |
|----------|-------------|------|
| id | Partner ID assigned by LiveIntent | string |
| name | not exposed in bid request | n/a |
| value | not exposed in bid request | n/a |

It is up to the partner to parse the segments and apply this information to their bid responses appropriately. 



## LiveIntent UI Users

Upon successful processing of a data file, the partner will be able to access the segment in the **Audiences** section of the [LiveIntent UI](http://lfm.liveintent.com). The partner supplied `segment name` will be displayed alongside LiveIntent's `segment ID` for reference. Once a segment is accessible in the UI, the partner may traffic **Strategies** using the segment as a target. 


