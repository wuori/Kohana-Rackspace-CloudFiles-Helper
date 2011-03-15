<?php

	/**
	 * @class Helper_Rackspace
	 * @author Michael Wuori <michael@wuori.com>
	 *
	 * Simple Helper to provide access to Rackspace Cloud API
	 * Requires Rackspace API Wrapper from rackspacecloud.com
	 * 
	 * 	Example Usage:
	 *	# upload to rackspace cloudfiles
	 *	# user helper/rackspace.php
	 *	$rackspace = Helper_Rackspace::factory($config['username'],$config['api_key']);
	 *		
	 *	# add file
	 *	$rackspace->from_file($config['container'],$filepath,$filename);
	 *		
	 *	# delete file
	 *	$rackspace->delete_file($config['container'],$filepath,$filename);
	 *		
	 */

	class Helper_Rackspace extends Controller
	{
	
		public $data = array();
		public $conn;
		public $container;

		/**
		 * Controller::before() (since we're going to access Kohana File Helper)
		 */
		public function before(){
			parent::before();
		}

		/**
		 * Create an instance of this class.
		 *
		 * @return object
		 */
		public function __construct($username=NULL,$api_key=NULL){
			
			# init
			require_once(APPPATH . 'classes/helper/rackspace/cloudfiles.php');
			if(!$username || !$api_key) exit('No Rackspace Credentials Provided');
			$auth = new CF_Authentication($username, $api_key);
			$auth->authenticate();
			$this->conn = new CF_Connection($auth);
		}

		/**
		 * Create an instance of this class.
		 *
		 * @return object
		 */
		public static function factory($username=NULL,$api_key=NULL)
		{
			return new Helper_Rackspace($username,$api_key);
		}

		/**
		 * Upload file to Rackspace CloudFiles
		 * Use by supplying uploaded (temporary in most cases) file via $filepath
		 *
		 * @return boolean
		 * @throws SyntaxException missing required parameters
		 * @throws BadContentTypeException if no Content-Type was/could be set
		 * @throws MisMatchedChecksumException $verify is set and checksums unequal
		 * @throws InvalidResponseException unexpected response
		 * @throws IOException error opening file
		 */
		public function from_file($container,$filepath,$filename)
		{
			# init
			$container = $this->conn->get_container($container);
			$file_object = $container->create_object($filename);
			
			# purge this from the Edge if already exists to bypass the TTL
			# at publish date, object only reaches CDN after "3 or so" requests
			# as such, put this in a try as to avoid CloudFiles exception
			try{ 
				$file_object->purge_from_cdn();
			} catch (Exception $e) {
				//exit($e->getMessage()); 
			}
			
			# insert mimetype as some php installs don't have the 
			# fxns that the Rackspace API wrapper asks for (mime_content_type(), etc.)
			$mime = File::mime($filepath); 
			$file_object->content_type = $mime;				
			
			return $file_object->load_from_filename($filepath);

		}
				
		/**
		 * Remove a file from Rackspace Cloudfiles
		 *
		 * @return boolean
		 * @throws SyntaxException invalid Object name
		 * @throws NoSuchObjectException remote Object does not exist
		 * @throws InvalidResponseException unexpected response
		 */
		public function delete_file($container,$filepath,$filename)
		{
			# init
			$container = $this->conn->get_container($container);
			
			# use get_object so we don't die when object doesn't exist
			$file_object  = $container->create_object($filename);
			
			# delete object
			$container->delete_object($filename);
		}
				
	}

?>