<?php

if (!function_exists('mirthe_tvshowblock_request')) {
    function mirthe_tvshowblock_request(string $url): array
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, kirby()->site()->title());
        $rawResponse = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        $data = json_decode($rawResponse, true);
        $failure = null;

        if ($rawResponse === false) {
            $failure = 'TMDB request failed: ' . $curlError;
        } elseif ($httpCode < 200 || $httpCode >= 300) {
            $failure = 'TMDB request returned HTTP ' . $httpCode;
        }

        if (!empty($data['status_message'])) {
            $failure = $data['status_message'];
            if ($httpCode < 200 || $httpCode >= 300) {
                $failure .= ' (HTTP ' . $httpCode . ')';
            }
        }

        return [
            'data' => $data,
            'failure' => $failure
        ];
    }
}

if (!function_exists('mirthe_tvshowblock_fetch')) {
    function mirthe_tvshowblock_fetch(string $tmdbid): array
    {
        $cache = kirby()->cache('mirthe.tvshowblock');
        $cacheKey = 'tmdb-tv-' . $tmdbid;
        $cachedData = $cache->get($cacheKey);

        if ($cachedData !== null) {
            return [
                'data' => $cachedData,
                'showFailure' => null,
                'creditsFailure' => null
            ];
        }

        $apiKey = option('themoviedb.apiKey');
        $baseUrl = 'https://api.themoviedb.org/3/tv/' . $tmdbid;
        $showResponse = mirthe_tvshowblock_request($baseUrl . '?api_key=' . $apiKey);
        $creditsResponse = mirthe_tvshowblock_request($baseUrl . '/credits?api_key=' . $apiKey);

        $data = [
            'show' => $showResponse['data'],
            'credits' => $creditsResponse['data']
        ];
        $cache->set($cacheKey, $data, 604800);

        return [
            'data' => $data,
            'showFailure' => $showResponse['failure'],
            'creditsFailure' => $creditsResponse['failure']
        ];
    }
}

if (!function_exists('mirthe_tvshowblock_log_error')) {
    function mirthe_tvshowblock_log_error(string $tmdbid, string $resource, string $reason): void
    {
        if (!function_exists('errorMonitorLog')) {
            return;
        }

        errorMonitorLog(
            'TVSHOWBLOCK',
            'TMDB ' . $resource . ' lookup failed for TV show ID ' . $tmdbid . ': ' . $reason,
            __FILE__,
            __LINE__
        );
    }
}