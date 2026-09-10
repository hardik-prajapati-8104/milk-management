<?php

namespace App\Support;

/**
 * Small, dependency-free User-Agent parser.
 *
 * This intentionally avoids pulling in a third-party package: it covers the
 * browsers/platforms/devices this ERP's users actually show up with
 * (desktop Chrome/Edge/Firefox/Safari, Android/iOS mobile browsers) using a
 * handful of ordered regex checks. Good enough for audit/analytics use,
 * not meant to be a bullet-proof replacement for a dedicated UA database.
 */
class UserAgentParser
{
    /**
     * @return array{browser: string, browser_version: ?string, platform: string, device_type: string}
     */
    public static function parse(?string $userAgent): array
    {
        $ua = trim((string) $userAgent);

        if ($ua === '') {
            return [
                'browser' => 'Unknown',
                'browser_version' => null,
                'platform' => 'Unknown',
                'device_type' => 'unknown',
            ];
        }

        return [
            'browser' => self::browser($ua),
            'browser_version' => self::browserVersion($ua),
            'platform' => self::platform($ua),
            'device_type' => self::deviceType($ua),
        ];
    }

    protected static function browser(string $ua): string
    {
        $patterns = [
            'Edge' => '/Edg(?:e|A|iOS)?\/[\d.]+/i',
            'Opera' => '/OPR\/[\d.]+|Opera\/[\d.]+/i',
            'Samsung Internet' => '/SamsungBrowser\/[\d.]+/i',
            'Facebook App' => '/FBAN|FBAV/i',
            'Instagram App' => '/Instagram/i',
            'Chrome' => '/Chrome\/[\d.]+/i',
            'Firefox' => '/Firefox\/[\d.]+/i',
            'Safari' => '/Version\/[\d.]+.*Safari/i',
            'Internet Explorer' => '/MSIE [\d.]+|Trident\/.*rv:[\d.]+/i',
        ];

        foreach ($patterns as $name => $pattern) {
            if (preg_match($pattern, $ua)) {
                return $name;
            }
        }

        if (self::isBot($ua)) {
            return 'Bot/Crawler';
        }

        return 'Other';
    }

    protected static function browserVersion(string $ua): ?string
    {
        $map = [
            '/Edg(?:e|A|iOS)?\/([\d.]+)/i',
            '/OPR\/([\d.]+)/i',
            '/Opera\/([\d.]+)/i',
            '/SamsungBrowser\/([\d.]+)/i',
            '/Chrome\/([\d.]+)/i',
            '/Firefox\/([\d.]+)/i',
            '/Version\/([\d.]+).*Safari/i',
            '/MSIE ([\d.]+)/i',
            '/rv:([\d.]+)\).*Gecko/i',
        ];

        foreach ($map as $pattern) {
            if (preg_match($pattern, $ua, $m)) {
                return $m[1];
            }
        }

        return null;
    }

    protected static function platform(string $ua): string
    {
        $patterns = [
            'Windows 11/10' => '/Windows NT 10\.0/i',
            'Windows 8.1' => '/Windows NT 6\.3/i',
            'Windows 8' => '/Windows NT 6\.2/i',
            'Windows 7' => '/Windows NT 6\.1/i',
            'Windows' => '/Windows NT [\d.]+/i',
            'iOS' => '/iPhone|iPad|iPod/i',
            'macOS' => '/Macintosh|Mac OS X/i',
            'Android' => '/Android/i',
            'Chrome OS' => '/CrOS/i',
            'Linux' => '/Linux/i',
        ];

        foreach ($patterns as $name => $pattern) {
            if (preg_match($pattern, $ua)) {
                return $name;
            }
        }

        return 'Unknown';
    }

    protected static function deviceType(string $ua): string
    {
        if (self::isBot($ua)) {
            return 'bot';
        }

        if (preg_match('/iPad|Tablet(?!.*Mobile)|Nexus (7|9|10)/i', $ua)) {
            return 'tablet';
        }

        if (preg_match('/Mobile|iPhone|iPod|Android.*Mobile|Windows Phone|BlackBerry/i', $ua)) {
            return 'mobile';
        }

        return 'desktop';
    }

    protected static function isBot(string $ua): bool
    {
        return (bool) preg_match('/bot|crawl|spider|slurp|bingpreview|facebookexternalhit|curl|wget|postman|insomnia/i', $ua);
    }
}
