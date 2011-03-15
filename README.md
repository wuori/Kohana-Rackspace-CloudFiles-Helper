# Rackspace Cloudfiles Helper

Simple helper to quickly use the Rackspace Cloudfiles API Wrapper within an existing Kohana application.

Example Usage:

# upload to rackspace cloudfiles
# use helper/rackspace.php
$rackspace = Helper_Rackspace::factory($config['username'],$config['api_key']);
		
# add file
$rackspace->from_file($config['container'],$filepath,$filename);
		
# delete file
$rackspace->delete_file($config['container'],$filepath,$filename);
