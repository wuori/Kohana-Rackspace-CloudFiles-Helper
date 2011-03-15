# Rackspace Cloudfiles Helper

Simple helper to quickly use the Rackspace Cloudfiles API Wrapper within an existing Kohana application.

Example Usage:

`#Start session:
$rackspace = Helper_Rackspace::factory($config['username'],$config['api_key']);
		
#Transfer file object: 
$rackspace->from_file($config['container'],$filepath,$filename);
		
#Remote file object:
$rackspace->delete_file($config['container'],$filepath,$filename);`
