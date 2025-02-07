<?php

namespace Aksara\Libraries;

/**
 * Miscellaneous Library
 * This class is used to generate any miscellanious features
 *
 * @author			Aby Dahana
 * @profile			abydahana.github.io
 * @website			www.aksaracms.com
 * @since			version 4.2.4
 * @copyright		(c) 2021 - Aksara Laboratory
 */

class Miscellaneous
{
	public function __construct()
	{
	}
	
	/**
	 * qrcode generator
	 */
	public function qrcode_generator($params = null)
	{
		$generator									= new \chillerlan\QRCode\QRCode();
		
		return $generator->render($params);
	}
	
	/**
	 * barcode generator
	 */
	public function barcode_generator($params = null)
	{
		$generator									= new \Picqer\Barcode\BarcodeGeneratorPNG();
		
		return 'data:image/png;base64,' . base64_encode($generator->getBarcode($params, $generator::TYPE_CODE_128, 1, 60));
	}
	
	/**
	 * shortlink generator
	 */
	public function shortlink_generator($params = null, $slug = null, $session = array())
	{
		if(!$params) return false;
		
		$this->model								= new \Aksara\Laboratory\Model();
		
		// hash generator
		$hash										= substr(sha1(uniqid(null, true)), -6);
		
		// check if hash already present
		if($this->model->get_where('app__shortlink', array('hash' => $hash), 1)->row())
		{
			// hash already present, repeat generator
			$this->shortlink_generator($params);
		}
		
		$checker									= $this->model->get_where('app__shortlink', array('url' => $params), 1)->row();
		
		// check if parameter already present
		if($checker)
		{
			$hash									= $checker->hash;
		}
		else
		{
			// no data present, insert one
			$this->model->insert
			(
				'app__shortlink',
				array
				(
					'hash'							=> $hash,
					'url'							=> $params,
					'session'						=> json_encode($session)
				)
			);
		}
		
		return base_url(($slug ? $slug : 'shortlink') . '/' . $hash);
	}
}
