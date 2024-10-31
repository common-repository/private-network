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
class pnPEM
{
	/**
	 * @param $pem String PEM formatted Private Key
	 * @param $passfrase String Optional passfrase if $pem in encrypted
	 *
	 * @return RSA object
	 */
	public static function ReadRSAPrivate($pem, $passfrase = '')
	{
		$pkey = null;
		if (empty($passfrase)) {
			$pkey = openssl_pkey_get_private($pem);
		}
		else {
			$pkey = openssl_pkey_get_private($pem, $passfrase);
		}
		if ($pkey === FALSE) {
			$exErr = "Error in PEM::ReadRSAPrivate()\n";
			while ($err = openssl_error_string()) {
				$exErr .= $err . "\n";
			}
			throw new Exception($exErr);
		}
		return new pnRSA($pkey);
	}

	/**
	 * @param $rsa RSA Object
	 * @param $passfrase String Option passfrase to encrypt Private Key
	 */
	public static function WriteRSAPrivate(pnRSA $rsa, $passfrase = '')
	{
		$ans = false;
		if (empty($passfrase)) {
			$ans = openssl_pkey_export($rsa->getPrivateKey(), $pem);
		}
		else {
			$ans = openssl_pkey_export($rsa->getPrivateKey(), $pem, $passfrase);
		}
		if ($ans === FALSE) {
			$exErr = "Error in PEM::WriteRSAPrivate()\n";
			while ($err = openssl_error_string()) {
				$exErr .= $err . "\n";
			}
			throw new Exception($exErr);
		}
		return $pem;
	}

	/**
	 * @param $pem String A PEM encoded X509 Certificate
	 *
	 * @return X509 object
	 */
	public static function ReadX509Cert($pem)
	{
		$res = openssl_x509_read($pem);
		if ($res === FALSE) {
			$exErr = "Error in PEM::ReadX509Cert()\n";
			while ($err = openssl_error_string()) {
				$exErr .= $err . "\n";
			}
			throw new Exception($exErr);			
		}
		return new pnX509($res);
	}

	public static function WriteX509Cert(pnX509 $x509)
	{
		if (!openssl_x509_export($x509->getCertificate(), $pem)) {
			$exErr = "Error in PEM::WriteX509Cert()\n";
			while ($err = openssl_error_string()) {
				$exErr .= $err . "\n";
			}
			throw new Exception($exErr);
		}
		return $pem;
	}
}