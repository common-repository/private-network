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
/**
 * A Self Signed X509 Certificate
 */
class pnX509
{
	protected $_x509;
	// this is the maximum size of data
	// encryptable using PKCS1_OAEP padding
	// and 1024 bits RSA keys
	const MAX_BLOCK_SIZE = 86;

	public function __construct($x509_resource = null)
	{
		$this->_x509 = $x509_resource;
	}

	public function __destruct()
	{
		if ($this->_x509 != null) {
			openssl_x509_free($this->_x509);
		}
	}

	/**
	 * @return row encrypted data
	 */
	public function encrypt($data)
	{
		$pkey = $this->getPublicKey();
		$chunks = str_split($data, self::MAX_BLOCK_SIZE);
		$output = '';

		foreach ($chunks as $chunk) {
			if (!openssl_public_encrypt($chunk, $crypted, $pkey, OPENSSL_PKCS1_OAEP_PADDING)) {
				$exErr = "Error in pnX509::encrypt()\n";
				while ($err = openssl_error_string()) {
					$exErr .= $err . "\n";
				}
				throw new Exception($exErr);	
			}
			$output .= $crypted;
		}
		return $output;
	}

	/**
	 * @param $data The signed data
	 * @param $signature The signature of the data
	 *
	 * @return TRUE if signature verifies FALSE otherwise
	 */
	public function verify($data, $signature)
	{
		$pkey = $this->getPublicKey();
		$ans = openssl_verify($data, $signature, $pkey);
		if ($ans == -1) {
			$exErr = "Error in pnX509::verify()\n";
			while ($err = openssl_error_string()) {
				$exErr .= $err . "\n";
			}
			throw new Exception($exErr);
		}
		return ($ans == 1);
	}

	/**
	 * Generate a self signed X509 certificate
	 *
	 * @param $dn array The Distinguished Name to be used in the certificate
	 * @param $rsa RSA object
	 * @param $days int Number of days the certificate will be valid for, default to 1 year
	 */
	public function generate(array $dn, pnRSA $rsa, $days = 365)
	{
		// Certificate Signing Request
		$csr = openssl_csr_new($dn, $rsa->getPrivateKey(), array('digest_alg', 'default_md'));
		if ($csr === FALSE) {
			$exErr = "Error in pnX509::generate()\n";
			while ($err = openssl_error_string()) {
				$exErr .= $err . "\n";
			}
			throw new Exception($exErr);			
		}
		// Self-signed certificate
		$this->_x509 = openssl_csr_sign($csr, null, $rsa->getPrivateKey(), $days);
		if ($this->_x509 === FALSE) {
			$exErr = "Error in pnX509::generate()\n";
			while ($err = openssl_error_string()) {
				$exErr .= $err . "\n";
			}
			throw new Exception($exErr);			
		}
	}

	public function getCertificate()
	{
		return $this->_x509;
	}

	public function getValidTo()
	{
		$des = $this->parse();
		return $des['validTo_time_t'];
	}

	public function getSubject()
	{
		$des = $this->parse();
		return $des['subject'];
	}


	public function parse()
	{
		if ($this->_x509 != null) {
			$sub = openssl_x509_parse($this->_x509, false);
			if ($sub === FALSE) {
				$exErr = "Error in pnX509::pase()\n";
				while ($err = openssl_error_string()) {
					$exErr .= $err . "\n";
				}
				throw new Exception($exErr);
			}
			return $sub;
		}
		return null;
	}

	public function getPublicKey()
	{
		if ($this->_x509 != null) {
			$pkey = openssl_pkey_get_public($this->_x509);
			if ($pkey === FALSE) {
				if (openssl_x509_export($this->_x509, $pem) == FALSE) {
					$exErr = "Error in pnX509::getPublicKey()\n";
					while ($err = openssl_error_string()) {
						$exErr .= $err . "\n";
					}
					throw new Exception($exErr);
				}
				$pkey = openssl_pkey_get_public($pem);
				if ($pkey === FALSE) {
					$exErr = "Error in pnX509::getPublicKey()\n";
					while ($err = openssl_error_string()) {
						$exErr .= $err . "\n";
					}
					throw new Exception($exErr);
				}
			}
			$pkey_data = openssl_pkey_get_details($pkey);
			if ($pkey_data === FALSE) {
				$exErr = "Error in pnX509::getPublicKey()\n";
				while ($err = openssl_error_string()) {
					$exErr .= $err . "\n";
				}
				throw new Exception($exErr);
			}
			return $pkey_data['key'];
		}
		return null;
	}
}