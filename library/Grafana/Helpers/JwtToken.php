<?php

namespace Icinga\Module\Grafana\Helpers;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtToken {
    const RSA_KEY_BITS = 2048;
    const JWT_PRIVATEKEY_FILE = '/etc/icingaweb2/modules/grafana/jwt.key.priv';
    const JWT_PUBLICKEY_FILE = '/etc/icingaweb2/modules/grafana/jwt.key.pub';


    /**
     * Create JWT Token
     */
    public static function create(string $sub, int $exp = 0, string $iss = null, array $claims = null) : string {
        $privateKeyFile = JwtToken::JWT_PRIVATEKEY_FILE;

        $privateKey = openssl_pkey_get_private(
            file_get_contents($privateKeyFile),
        );

        $payload = [
            'sub' => $sub,
            'iat' => time(),
            'nbf' => time(),
        ];

        if(isset($claims)) {
            $payload = array_merge($payload, $claims);
        }

        if (!empty($iss)) {
            $payload['iss'] = $iss;
        }
        if ($exp > 0) {
            $payload['exp'] = $exp;
        }

        return JWT::encode($payload, $privateKey, 'RS256');
    }

    /**
     * Generate Private and Public RSA Keys
     */
    public static function generateRsaKeys()
    {
        if(!file_exists(JwtToken::JWT_PRIVATEKEY_FILE)) {
            $config = array(
                "private_key_bits" => JwtToken::RSA_KEY_BITS,
                "private_key_type" => OPENSSL_KEYTYPE_RSA,
            );

            $res = openssl_pkey_new($config);
            openssl_pkey_export($res, $privKey);
            $pubKey = openssl_pkey_get_details($res);
            $pubKey = $pubKey["key"];

            file_put_contents(JwtToken::JWT_PRIVATEKEY_FILE, $privKey);
            file_put_contents(JwtToken::JWT_PUBLICKEY_FILE, $pubKey);
        }
    }
}
