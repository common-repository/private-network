<?php
/*  Copyright 2008  Andrea Belvedere  (email : scieck at gmail dot com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
class pnRSA
{
	protected $_priKey;
	protected $_pubKey;

	/**
	 * @param $rsa_resource A Private Key resource
	 */
	public function __construct($rsa_resource = null)
	{
		if (is_null($rsa_resource)) {
			$this->_priKey = null;
			$this->_pubKey = null;
		}
		else {
			$this->_priKey = $rsa_resource;
			$this->_pubKey = $this->extractPublicKey($this->_priKey);
		}
	}
	
	public function __destruct()
	{
		if ($this->_priKey != null) {
			openssl_free_key($this->_priKey);
		}
	}

	/**
	 * Generate an RSA key pair
	 *
	 * @param $strength int Strength in bits of the RSA key, default to 1024
	 */
	public function generate($strength = 1024)
	{
		if (empty($strength) || !is_numeric($strength) || ($strength < 1024)) {
			throw new Exception("Key strenght must be equal or above 1024 bits");
		}
		$this->_priKey = openssl_pkey_new(array('private_key_bits' => $strength,
												'private_key_type' => OPENSSL_KEYTYPE_RSA));
		if ($this->_priKey === FALSE) {
			$exErr = "Error in pnRSA::generate()\n";
			while ($err = openssl_error_string()) {
				$exErr .= $err . "\n";
			}
			throw new Exception($exErr);
		}
		$this->_pubKey = $this->extractPublicKey($this->_priKey);
	}

	/**
	 * Compute digital signature of sha1($data)
	 *
	 * @param $data The data to sign
	 */
	public function sign($data)
	{
		if (!openssl_sign($data, $signature, $this->_priKey)) {
			$exErr = "Error in pnRSA::sign()\n";
			while ($err = openssl_error_string()) {
				$exErr .= $err . "\n";
			}
			throw new Exception($exErr);
		}
		return $signature;
	}

	/**
	 * Decrypt the passed $data
	 *
	 * @param $data String (bytes) The encrypted data
	 *
	 * @return The decripted data
	 */
	public function decrypt($data)
	{
		$key = openssl_pkey_get_details($this->_priKey);
		if ($key === FALSE) {
			$exErr = "Error in pnRSA::decrypt()\n";
			while ($err = openssl_error_string()) {
				$exErr .= $err . "\n";
			}
			throw new Exception($exErr);			
		}
		$block_size = $key['bits'] / 8;
		$chunks = str_split($data, $block_size);
		$output = '';
		foreach ($chunks as $chunk) {
			if (!openssl_private_decrypt($chunk, $decrypted, $this->_priKey, OPENSSL_PKCS1_OAEP_PADDING)) {
				$exErr = "Error in pnRSA::decrypt()\n";
				while ($err = openssl_error_string()) {
					$exErr .= $err . "\n";
				}
				throw new Exception($exErr);		
			}
			$output .= $decrypted;
		}
		return $output;
	}

	/**
	 * The returned private key contains both private and public key
	 *
	 * @return resource Private & Public Key
	 */
	public function getPrivateKey()
	{
		return $this->_priKey;
	}

	/**
	 * @return resource A Public Key
	 */
	public function getPublicKey()
	{
		return $this->_pubKey;
	}

	/**
	 * @param $pri resource A Private Key
	 *
	 * @return resource A Public Key
	 */
	public function extractPublicKey($pri)
	{
		$det = openssl_pkey_get_details($pri);
		if ($det === FALSE) {
			$exErr = "Error in pnRSA::extractPublicKey()\n";
			while ($err = openssl_error_string()) {
				$exErr .= $err . "\n";
			}
			throw new Exception($exErr);
		}
		$pub = openssl_pkey_get_public($det['key']);
		if ($pub === FALSE) {
			$exErr = "Error in pnRSA::extractPublicKey()\n";
			while ($err = openssl_error_string()) {
				$exErr .= $err . "\n";
			}
			throw new Exception($exErr);
		}
		return $pub;
	}
}