<?php 
/**
 * Template Name: LiveAudience API
 *
 * The template for displaying the LiveAudience API.
 */
?>
 <head>
    <meta charset="utf-8">
    <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>LiveAudience API</title>

    <link href="http://lidevelopers.staging.wpengine.com/wp-content/themes/morpheus-child/css/screen.css" rel="stylesheet" type="text/css" media="screen" />
    <link href="http://lidevelopers.staging.wpengine.com/wp-content/themes/morpheus-child/css/print.css" rel="stylesheet" type="text/css" media="print" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
      <script src="http://lidevelopers.staging.wpengine.com/wp-content/themes/morpheus-child/js/all.js" type="text/javascript"></script>

      <script>
        $(function() {
          setupLanguages(["Code Examples"]);
        });
      </script>

      <style>
      .content table td{
        font-size:13px !important;
      }


      </style>
  </head>

  <body class="index">
    <a href="#" id="nav-button">
      <span>
        NAV
        <img src="http://developers.liveintent.com/wp-content/uploads/2015/03/navbar.png" />
      </span>
    </a>
    <div class="tocify-wrapper">
      <img src="http://developers.liveintent.com/wp-content/uploads/2015/03/logo.png" />
        <div class="lang-selector">
              <a href="#" data-language-name="Code Examples">Code Examples</a>
        </div>
        <div class="search">
          <input type="text" class="search" id="input-search" placeholder="Search">
        </div>
        <ul class="search-results"></ul>
      <div id="toc">
      </div>
        <ul class="toc-footer">
            <li><a href='mailto:DeveloperSupport@liveintent.com?Subject=Developer%20support'>Need help? E-mail us!</a></li>
        </ul>
    </div>
    <div class="page-wrapper">
      <div class="dark-box"></div>
      <div class="content">
        <h1 id="introduction">Introduction</h1>

<p>LiveIntent supports the use of partner-provided 1st-party data for use in targeting ad campaigns. This document describes the process whereby partners can supply 1st-party data files and segmentation information to LiveIntent for subsequent targeting in either programmatic transactions or trafficking campaigns directly in the LiveIntent UI.  </p>

<h1 id="change-history">Change History</h1>

<table><thead>
<tr>
<th>Date</th>
<th>Responsible</th>
<th>Change</th>
</tr>
</thead><tbody>
<tr>
<td>11/21/2014</td>
<td>Dave Wright</td>
<td>Created v1.1:</td>
</tr>
<tr>
<td></td>
<td></td>
<td>Added use case of using API to upload Audience data into LiveIntent API.</td>
</tr>
<tr>
<td></td>
<td></td>
<td>Modified protocol to use sftp only for transfer of email hashes, metadata containing segment ids, and acknowledgement.</td>
</tr>
<tr>
<td>12/12/2014</td>
<td>Dave Wright</td>
<td>Created v1.2:</td>
</tr>
<tr>
<td></td>
<td></td>
<td>Sub-id is required for agencies, optional for others.</td>
</tr>
<tr>
<td></td>
<td></td>
<td>Address errors in initial metadata upload by putting metadata validation response into sftp directory. Updated error codes.</td>
</tr>
<tr>
<td></td>
<td></td>
<td>Provide naming conventions for files in sftp.</td>
</tr>
<tr>
<td>08/09/2016</td>
<td>Kyle Brown</td>
<td>Created v1.3:</td>
</tr>
<tr>
<td></td>
<td></td>
<td>Minor updates to reflect current implementation.</td>
</tr>
</tbody></table>

<h1 id="overview">Overview</h1>

<h2 id="basic-workflow">Basic Workflow</h2>

<p>This basic workflow for placing 1st-party data files into service on the LiveIntent platform is:</p>

<ol>
<li><p>Partner receives data file from agency, ATD, advertiser, or publisher. This file contains MD5, SHA1 or SHA2 hashes of email addresses to be targeted.</p></li>
<li><p>Partner creates segment metadata file describing segmentation identifiers for the email addresses.</p></li>
<li><p>Partner and LiveIntent coordinate SFTP endpoint and SSH public key for authentication checks.</p></li>
<li><p>Data files of email hashes <code class="prettyprint">&lt;filename&gt;</code> and metadata describing segments for the email addresses <code class="prettyprint">&lt;filename&gt;.json</code> are uploaded via SFTP to LiveIntent. LiveIntent validates the metadata and provides a validation summary <code class="prettyprint">&lt;filename&gt;.status</code> in the SFTP directory.</p></li>
<li><p>Using the supplied metadata, the LiveIntent platform processes the hash file.</p></li>
<li><p>Upon completion of processing the file, a <code class="prettyprint">&lt;filename&gt;.status</code> file containing this success/failure status will be place in the LiveIntent SFTP directory.</p></li>
<li><p>Segment data is made available for targeting on LiveIntent inventory: </p>

<ol>
<li><a href="#liveintent-ui-users"><strong>LiveIntent UI Users</strong></a>: Segments may be managed and targeted to ad campaigns within the LiveIntent UI.</li>
<li><a href="#programmatic-transactions"><strong>Programmatic Bidders</strong></a>: As appropriate, LiveIntent supplies segment data to partner during subsequent programmatic transactions.</li>
</ol></li>
</ol>

<h2 id="sftp-file-transfers">SFTP File Transfers</h2>

<p>LiveIntent will supply each partner with information regarding the secure uploading of data files to a LiveIntent server, including destination and credentials. Please consult with your LiveIntent integration contact for details.</p>

<ul>
<li><p>The metadata file should have a .json extension: <code class="prettyprint">&lt;filename&gt;.json</code>.</p></li>
<li><p>Once LiveIntent processes the metadata file, it will create a
<code class="prettyprint">&lt;filename&gt;.status</code> file for errors in the metadata; this response should be
available in the SFTP folder within 30 minutes.</p></li>
<li><p>Once the first party file has been processed, LiveIntent will create a <code class="prettyprint">&lt;filename&gt;.status</code> file, indicating processed status; this response might take up to 24 hours.</p></li>
</ul>

<aside class="notice">
The **_\<filename\>_** should be consistent for all files, e.g. if the partner uploads **_my_request.json**, LiveIntent will create **_my_request.status**. The metadata file should have the same name as the data file as a best practice, but is not mandatory.
</aside>

<h2 id="file-formats">File Formats</h2>

<h3 id="email-hash-data-file">Email hash data file</h3>

<p>The 1st-party email hash data file should be in CSV format and consist of a single column of <em>entirely</em> MD5, SHA1 or SHA2 hashed email addresses. (Important: Email addresses must be in lowercase format prior to hashing; failure to adhere to this specification will result in user-targeting failure.) Files may be compressed in either .GZ or .ZIP format for efficiency.</p>

<h3 id="segmentation-metadata-file">Segmentation metadata file</h3>

<p>The 1st party segmentation metadata file contains a description of partner owned segments to be added or removed. If a specified segment does not exist, it will be created.</p>

<p>The content of the message body must be JSON and adhere to the following structure. All fields are <em>required</em> unless indicated otherwise.</p>

<p>The <code class="prettyprint">file</code> object contains metadata about the data file in order to verify that the processing is to be applied to a specific email hash data file.</p>

<p>The <code class="prettyprint">segments</code> array accepts multiple <code class="prettyprint">segment</code> objects, each with
an <code class="prettyprint">action</code> parameter. This way the partner can modify one or more segments in a single call, applying the data file as either an addition or removal in each case.</p>

<h1 id="request-body">Request Body</h1>
<pre class="highlight json"><code><span class="w">

</span><span class="p">{</span><span class="w">
  </span><span class="nt">"partner_id"</span><span class="w"> </span><span class="p">:</span><span class="w"> </span><span class="s2">"c9eefe7ba1861a601d01a0f3e8f25573"</span><span class="p">,</span><span class="w"> 
  </span><span class="nt">"sub_id"</span><span class="w"> </span><span class="p">:</span><span class="w"> </span><span class="s2">"142da56bcfbc547dc206e0952edb6214"</span><span class="p">,</span><span class="w"> 
  </span><span class="nt">"file"</span><span class="w"> </span><span class="p">:</span><span class="w"> </span><span class="p">{</span><span class="w">
    </span><span class="nt">"name"</span><span class="w"> </span><span class="p">:</span><span class="w"> </span><span class="s2">"435ed7e9f07f740abf511a62c00eef6e.txt&gt;"</span><span class="p">,</span><span class="w">
  </span><span class="nt">"hash"</span><span class="w"> </span><span class="p">:</span><span class="w"> </span><span class="s2">"md5"</span><span class="p">,</span><span class="w">
  </span><span class="nt">"digest"</span><span class="w"> </span><span class="p">:</span><span class="w"> </span><span class="s2">"435ed7e9f07f740abf511a62c00eef6e"</span><span class="p">,</span><span class="w"> </span><span class="nt">"date"</span><span class="w"> </span><span class="p">:</span><span class="w"> </span><span class="s2">"2014-12-12"</span><span class="p">,</span><span class="w">
  </span><span class="nt">"records"</span><span class="w"> </span><span class="p">:</span><span class="w"> </span><span class="mi">1000000</span><span class="w">
  </span><span class="p">},</span><span class="w">
  </span><span class="nt">"segments"</span><span class="w"> </span><span class="p">:</span><span class="w"> </span><span class="p">[</span><span class="w"> 
  </span><span class="p">{</span><span class="w">
  </span><span class="nt">"id"</span><span class="w"> </span><span class="p">:</span><span class="w"> </span><span class="s2">"123"</span><span class="p">,</span><span class="w"> 
  </span><span class="nt">"name"</span><span class="w"> </span><span class="p">:</span><span class="w"> </span><span class="s2">"Recent Purchasers"</span><span class="p">,</span><span class="w"> 
  </span><span class="nt">"action"</span><span class="w"> </span><span class="p">:</span><span class="w"> </span><span class="s2">"add"</span><span class="w">
  </span><span class="p">},</span><span class="w">
  </span><span class="p">{</span><span class="w">
  </span><span class="nt">"id"</span><span class="w"> </span><span class="p">:</span><span class="w"> </span><span class="s2">"456"</span><span class="p">,</span><span class="w"> 
  </span><span class="nt">"name"</span><span class="w"> </span><span class="p">:</span><span class="w"> </span><span class="s2">"Inactive Subscribers"</span><span class="p">,</span><span class="w"> 
  </span><span class="nt">"action"</span><span class="w"> </span><span class="p">:</span><span class="w"> </span><span class="s2">"remove"</span><span class="w">
  </span><span class="p">}</span><span class="w">
 </span><span class="p">]</span><span class="w">
</span><span class="p">}</span><span class="w">
</span></code></pre>

<table><thead>
<tr>
<th>Property</th>
<th>Description</th>
<th>Type</th>
</tr>
</thead><tbody>
<tr>
<td>partner_id</td>
<td>Partner ID (assigned by LiveIntent)</td>
<td>string</td>
</tr>
<tr>
<td>sub_id</td>
<td>Sub ID (assigned by LiveIntent). This field may be used by UI Partners to specify a partner-managed advertiser.</td>
<td>string</td>
</tr>
<tr>
<td>file</td>
<td>Data file metadata</td>
<td>object</td>
</tr>
<tr>
<td>name</td>
<td>Name of file as <em>&lt;digest_value&gt;.txt</em></td>
<td>string</td>
</tr>
<tr>
<td>hash</td>
<td>Designates the hashing algorithm used to derive the digest of the file.</td>
<td>&ldquo;md5&rdquo; or &ldquo;sha1&rdquo;</td>
</tr>
<tr>
<td>digest</td>
<td>Calculated message digest of the file based on hash</td>
<td>string</td>
</tr>
<tr>
<td>date</td>
<td>Date of file upload</td>
<td>yyyy-mm-dd</td>
</tr>
<tr>
<td>records</td>
<td>Number of records in file</td>
<td>integer</td>
</tr>
<tr>
<td>segments</td>
<td>Array of segment objects</td>
<td>array of objects</td>
</tr>
<tr>
<td>id</td>
<td>Partner defined segment ID.  Must be integer values only. Other values will be rejected and the segment action will fail.  It is up to the partner to ensure that there is no possibility of collisions across segment IDs within their platform.</td>
<td>string</td>
</tr>
<tr>
<td>name</td>
<td>Partner segment name</td>
<td>string</td>
</tr>
<tr>
<td>action</td>
<td>Indicates whether hashes in the data file should be added to or removed from segment</td>
<td>&ldquo;add&rdquo; or &ldquo;remove&rdquo;</td>
</tr>
</tbody></table>

<h1 id="processing-status">Processing Status</h1>

<p>After attempting to process the data file as directed by the metadata, LiveIntent will provide status to the partner acknowledging the attempt and indicating the result of the operation. This status will be provided as <code class="prettyprint">&lt;filename&gt;.status</code> in the SFTP folder. It is the responsibility of the partner to retrieve and process the status file and take further action if necessary. </p>

<p>The content of the status file will be JSON format. In the event of a successful operation, the file will contain the file metadata and segment information as well as a LiveIntent generated <code class="prettyprint">transaction_id</code>. The structure of the file will be as follows: </p>
<pre class="highlight json"><code><span class="p">{</span><span class="w">
  </span><span class="nt">"transaction_id"</span><span class="w"> </span><span class="p">:</span><span class="w"> </span><span class="s2">"12345678"</span><span class="p">,</span><span class="w">
  </span><span class="nt">"partner_id"</span><span class="w"> </span><span class="p">:</span><span class="w"> </span><span class="s2">"1001"</span><span class="p">,</span><span class="w"> 
  </span><span class="nt">"sub_id"</span><span class="w"> </span><span class="p">:</span><span class="w"> </span><span class="s2">"476378"</span><span class="p">,</span><span class="w"> 
  </span><span class="nt">"file"</span><span class="w"> </span><span class="p">:</span><span class="w"> </span><span class="p">{</span><span class="w">
    </span><span class="nt">"name"</span><span class="w"> </span><span class="p">:</span><span class="w"> </span><span class="s2">"435ed7e9f07f740abf511a62c00eef6e.txt&gt;"</span><span class="p">,</span><span class="w">
    </span><span class="nt">"hash"</span><span class="w"> </span><span class="p">:</span><span class="w"> </span><span class="s2">"md5"</span><span class="p">,</span><span class="w">
    </span><span class="nt">"digest"</span><span class="w"> </span><span class="p">:</span><span class="w"> </span><span class="s2">"435ed7e9f07f740abf511a62c00eef6e"</span><span class="p">,</span><span class="w"> 
    </span><span class="nt">"date"</span><span class="w"> </span><span class="p">:</span><span class="w"> </span><span class="s2">"2014-12-12"</span><span class="p">,</span><span class="w">
    </span><span class="nt">"records"</span><span class="w"> </span><span class="p">:</span><span class="w"> </span><span class="mi">1000000</span><span class="w">
  </span><span class="p">},</span><span class="w">
  </span><span class="nt">"segments"</span><span class="w"> </span><span class="p">:</span><span class="w"> </span><span class="p">[</span><span class="w"> 
  </span><span class="p">{</span><span class="w">
    </span><span class="nt">"id"</span><span class="w"> </span><span class="p">:</span><span class="w"> </span><span class="s2">"123"</span><span class="p">,</span><span class="w">
    </span><span class="nt">"action"</span><span class="w"> </span><span class="p">:</span><span class="w"> </span><span class="s2">"add"</span><span class="p">,</span><span class="w">
    </span><span class="nt">"code"</span><span class="w"> </span><span class="p">:</span><span class="w"> </span><span class="s2">"code"</span><span class="p">,</span><span class="w">
    </span><span class="nt">"records"</span><span class="w"> </span><span class="p">:</span><span class="w"> </span><span class="mi">1000000</span><span class="w">
  </span><span class="p">},</span><span class="w">
  </span><span class="p">{</span><span class="w">
    </span><span class="nt">"id"</span><span class="w"> </span><span class="p">:</span><span class="w"> </span><span class="s2">"456"</span><span class="p">,</span><span class="w"> 
    </span><span class="nt">"action"</span><span class="w"> </span><span class="p">:</span><span class="w"> </span><span class="s2">"remove"</span><span class="p">,</span><span class="w">
    </span><span class="nt">"code"</span><span class="w"> </span><span class="p">:</span><span class="w"> </span><span class="s2">"code"</span><span class="p">,</span><span class="w">
    </span><span class="nt">"records"</span><span class="w"> </span><span class="p">:</span><span class="w"> </span><span class="mi">1000000</span><span class="w"> 
  </span><span class="p">}</span><span class="w">
 </span><span class="p">]</span><span class="w">
</span><span class="p">}</span><span class="w">


</span></code></pre>

<h1 id="errors">Errors</h1>
<pre class="highlight json"><code><span class="p">{</span><span class="w">
</span><span class="nt">"error"</span><span class="w"> </span><span class="p">:</span><span class="w"> </span><span class="p">{</span><span class="w"> 
  </span><span class="nt">"code"</span><span class="w"> </span><span class="p">:</span><span class="w"> </span><span class="s2">" 8"</span><span class="p">,</span><span class="w"> 
  </span><span class="nt">"description"</span><span class="w"> </span><span class="p">:</span><span class="w"> </span><span class="s2">"File has no valid records"</span><span class="w">
 </span><span class="p">}</span><span class="w"> 
</span><span class="p">}</span><span class="w"> 

</span></code></pre>

<p>In the event of a failed operation, an error message will be returned.</p>

<table><thead>
<tr>
<th>Code</th>
<th>Description</th>
</tr>
</thead><tbody>
<tr>
<td>0</td>
<td>Internal error</td>
</tr>
<tr>
<td>1</td>
<td>Request no well formed</td>
</tr>
<tr>
<td>2</td>
<td>Partner id invalid or missing</td>
</tr>
<tr>
<td>3</td>
<td>Sub id invalid or missing</td>
</tr>
<tr>
<td>4</td>
<td>Filename contains slash characters</td>
</tr>
<tr>
<td>5</td>
<td>Callback URL wrong format</td>
</tr>
<tr>
<td>6</td>
<td>File not found</td>
</tr>
<tr>
<td>7</td>
<td>File checksum validation failed</td>
</tr>
<tr>
<td>8</td>
<td>File has no valid records</td>
</tr>
</tbody></table>

<h1 id="accessing-segment-data">Accessing Segment Data</h1>

<p>LiveIntent allows partners to target their audiences directly in the LiveIntent UI or via programmatic bidding.</p>

<h2 id="programmatic-transactions">Programmatic Transactions</h2>
<pre class="highlight json"><code><span class="p">{</span><span class="w">
  </span><span class="nt">"data"</span><span class="p">:</span><span class="w"> </span><span class="p">[</span><span class="w">
    </span><span class="p">{</span><span class="w">
      </span><span class="nt">"id"</span><span class="p">:</span><span class="w"> </span><span class="s2">"1"</span><span class="p">,</span><span class="w">
      </span><span class="nt">"name"</span><span class="p">:</span><span class="w"> </span><span class="s2">"Partner"</span><span class="p">,</span><span class="w">
      </span><span class="nt">"segment"</span><span class="p">:</span><span class="w"> </span><span class="p">[</span><span class="w"> 
        </span><span class="p">{</span><span class="nt">"id"</span><span class="p">:</span><span class="w"> </span><span class="s2">"123"</span><span class="p">},</span><span class="w">
        </span><span class="p">{</span><span class="nt">"id"</span><span class="p">:</span><span class="w"> </span><span class="s2">"456"</span><span class="p">}</span><span class="w">
      </span><span class="p">]</span><span class="w">
    </span><span class="p">}</span><span class="w">
  </span><span class="p">]</span><span class="w">
</span><span class="p">}</span><span class="w">

</span></code></pre>

<p>LiveIntent will communicate segment data to the partner within the bid request object, as appropriate, using existing mechanisms in the <a href="http://www.iab.net/media/file/OpenRTBAPISpecificationVersion2_2.pdf">OpenRTB specification</a>. We support the following attributes in the <code class="prettyprint">data</code> object child of the <code class="prettyprint">user</code> object for this purpose: </p>

<p>Data object:</p>

<table><thead>
<tr>
<th>Property</th>
<th>Description</th>
<th>Type</th>
</tr>
</thead><tbody>
<tr>
<td>id</td>
<td>Partner ID assigned by LiveIntent</td>
<td>string</td>
</tr>
<tr>
<td>name</td>
<td>Partner name</td>
<td>string</td>
</tr>
<tr>
<td>segment</td>
<td>Array of segment objects</td>
<td>array</td>
</tr>
</tbody></table>

<p>Segment object:</p>

<table><thead>
<tr>
<th>Property</th>
<th>Description</th>
<th>Type</th>
</tr>
</thead><tbody>
<tr>
<td>id</td>
<td>Partner ID assigned by LiveIntent</td>
<td>string</td>
</tr>
<tr>
<td>name</td>
<td>not exposed in bid request</td>
<td>n/a</td>
</tr>
<tr>
<td>value</td>
<td>not exposed in bid request</td>
<td>n/a</td>
</tr>
</tbody></table>

<p>It is up to the partner to parse the segments and apply this information to their bid responses appropriately. </p>

<h2 id="liveintent-ui-users">LiveIntent UI Users</h2>

<p>Upon successful processing of a data file, the partner will be able to access the segment in the <strong>Audiences</strong> section of the <a href="http://lfm.liveintent.com">LiveIntent UI</a>. The partner supplied <code class="prettyprint">segment name</code> will be displayed alongside LiveIntent&rsquo;s <code class="prettyprint">segment ID</code> for reference. Once a segment is accessible in the UI, the partner may traffic <strong>Strategies</strong> using the segment as a target. </p>

      </div>
      <div class="dark-box">
          <div class="lang-selector">
                <a href="#" data-language-name="Code Examples">Code Examples</a>
          </div>
      </div>
    </div>
  </body>
