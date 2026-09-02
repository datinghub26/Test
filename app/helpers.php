<?php

if (!function_exists('sub_percentage')) {
    function sub_percentage($number, $percentage): float|int
    {
        $amount_to_sub = ($number * $percentage) / 100;
        return $number - $amount_to_sub;
    }
}


if (!function_exists('to_money')) {
    function to_money_str($coins, $decimals = 2): string
    {
        return number_format($coins / 1000, $decimals);
    }

    function to_money($coins): float
    {
        return round($coins / 1000, 3);
    }

    function format_coins($coins): string
    {
        if ($coins < 1 && $coins > 0) {
            return number_format($coins, 2);
        }
        return number_format($coins);
    }
}

if (!function_exists('ip')) {
    function ip(): string
    {
        //return '41.44.196.187';

        return request()->server('HTTP_CF_CONNECTING_IP') ?? request()->ip();
    }
}

if (!function_exists('country_code')) {
    function country_code(): ?string
    {
        if (config('location.testing.enabled') && ip() == '127.0.0.1') {
            return 'EG';
        }

        $countryCode = request()->server('HTTP_CF_IPCOUNTRY');
        if ($countryCode) {
            return $countryCode;
        }

        try {
            $position = Location::get(ip());
            return $position->countryCode ?? null;
        } catch (Exception $e) {
            return null;
        }
    }
}

if (!function_exists('percentage_value')) {
    function percentage_value($number, $percentage): float|int
    {
        return $number * $percentage / 100;
    }
}

if (!function_exists('device_image')) {
    function device_image($device): string
    {
        return match ($device) {
            'Windows' => asset('assets/img/devices/windows-applications-svgrepo-com.svg'),
            'Mac' => asset('assets/img/devices/pc-svgrepo-com.svg'),
            'Android' => asset('assets/img/devices/android-svgrepo-com.svg'),
            'iOS' => asset('assets/img/devices/apple-logo-svgrepo-com.svg'),
            default => asset('assets/img/devices/question-svgrepo-com.svg'),
        };
    }
}

if (!function_exists('browser_image')) {
    function browser_image($browser): string
    {
        return match ($browser) {
            'Chrome' => asset('assets/img/devices/chrome-svgrepo-com.svg'),
            'Firefox' => asset('assets/img/devices/firefox-svgrepo-com.svg'),
            'Safari' => asset('assets/img/devices/safari-svgrepo-com.svg'),
            'Edge' => asset('assets/img/devices/edge-svgrepo-com.svg'),
            'Opera' => asset('assets/img/devices/opera-svgrepo-com.svg'),
            default => asset('assets/img/devices/question-svgrepo-com.svg'),
        };
    }
}

if(!function_exists('report_to')) {
    function report_to($e, $channel = 'single', $context = []): void
    {
        Log::channel($channel)->error(
            $e->getMessage(),
            array_merge(
                Context::getContext(),
                $context,
                ['exception' => $e],
            )
        );
    }

}
