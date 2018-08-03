<?php

namespace Icinga\Module\Analytics\ProvidedHook;

use Icinga\Application\Hook\AuthenticationHook;
use Icinga\Application\Logger;
use Icinga\User;

class Authentication extends AuthenticationHook
{
    const GRAFANA_SESS = "grafana_sess";

    public function onLogin(User $user)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, "http://localhost:3000/login");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(sprintf("X-WEBAUTH-USER: %s", $user->getUsername())));
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        if (!$result = curl_exec($ch)) {
            // don't fail but log the possible errors
            Logger::error(new \Exception(curl_error($ch)));
            return;
        }

        $preg = sprintf('/^\s*Set-Cookie:\s+%s=(.*?);.*$/mi', self::GRAFANA_SESS);
        preg_match($preg, $result, $matches);

        if (count($matches) < 2) {
            Logger::error(new \Exception("Login to grafana did not return any grafana_sess"));
            return;
        }

        $session = $matches[1];

        if (!setcookie(self::GRAFANA_SESS, $session, 0, "/grafana", null, false, true)) {
            Logger::error(new \Exception("Failed to set grafana_sess"));
            return;
        }

        return;
    }

    public function onLogout(User $_)
    {
        // remove the cookie by set it to null
        // this will remove this cookie from all tabs and windows
        // but this will not logout the user from different browsers or devices
        if (!setcookie(self::GRAFANA_SESS, null, 0, "/grafana", null, false, true)) {
            Logger::error(new \Exception("Failed to set grafana_sess"));
            return;
        }

        return;
    }
}
