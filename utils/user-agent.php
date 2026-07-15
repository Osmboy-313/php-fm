<?php

// A simple User Agent parser, didn't have time to write my own, so got it written from ChatGpt, if it works, it works ;)
// I know it's limitation though, best practice is to use a well known library that does this job perfectly but umm I didn't need that, so...

function getClientInfo(): array{
    $ua = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';

    $browser = 'Unknown';

    if (str_contains($ua, 'Edg')) {
        $browser = 'Edge';
    } elseif (str_contains($ua, 'Chrome')) {
        $browser = 'Chrome';
    } elseif (str_contains($ua, 'Firefox')) {
        $browser = 'Firefox';
    } elseif (str_contains($ua, 'Safari')) {
        $browser = 'Safari';
    }

    $os = 'Unknown';

    if (preg_match('/Windows/i', $ua)) {
        $os = 'Windows';
    } elseif (preg_match('/Android/i', $ua)) {
        $os = 'Android';
    } elseif (preg_match('/iPhone|iPad/i', $ua)) {
        $os = 'iOS';
    } elseif (preg_match('/Mac OS X/i', $ua)) {
        $os = 'macOS';
    } elseif (preg_match('/Linux/i', $ua)) {
        $os = 'Linux';
    }

    if (preg_match('/iPad|Tablet/i', $ua)) {
        $device = 'Tablet';
    } elseif (preg_match('/Mobile|Android|iPhone/i', $ua)) {
        $device = 'Mobile';
    } else {
        $device = 'Desktop';
    }

    return [
        'ip' => $ip,
        'ip_version' => filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)
            ? 'IPv6'
            : 'IPv4',
        'browser' => $browser,
        'os' => $os,
        'device' => $device,
        'user_agent' => $ua,
    ];
}


?>