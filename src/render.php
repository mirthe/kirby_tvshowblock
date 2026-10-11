<?php

if (!function_exists('mirthe_tvshowblock_render')) {
    function mirthe_tvshowblock_render(string $tmdbid): string
    {
        $result = mirthe_tvshowblock_fetch($tmdbid);
        $showData = $result['data'];
        $showInfo = $showData['show'] ?? null;
        $credits = $showData['credits'] ?? null;

        if (!empty($result['creditsFailure'])) {
            mirthe_tvshowblock_log_error($tmdbid, 'credits', $result['creditsFailure']);
        }

        if (empty($showInfo) || !is_array($showInfo) || isset($showInfo['status_code'])) {
            $failureReason = $result['showFailure'] ?? $showInfo['status_message'] ?? 'TMDB returned no TV show data.';
            if (isset($showInfo['status_code'])) {
                $failureReason .= ' (TMDB status ' . $showInfo['status_code'] . ')';
            }
            mirthe_tvshowblock_log_error($tmdbid, 'TV', $failureReason);

            return '<div class="well"><div class="well-body">TV-show niet gevonden</div></div>';
        }

        $poster = isset($showInfo['poster_path']) ? 'https://www.themoviedb.org/t/p/w200/' . $showInfo['poster_path'] : '';
        $output = '<div class="well">';
        if ($poster !== '') {
            $output .= '<div class="well-img"><a href="https://www.themoviedb.org/tv/' . $showInfo['id'] . '"><img src="' . $poster . '" alt=""></a></div>';
        }
        $output .= '<div class="well-body">';
        $output .= '<p><a href="https://www.themoviedb.org/tv/' . $showInfo['id'] . '">' . htmlspecialchars($showInfo['name'] ?? '', ENT_QUOTES) . '</a><br>';
        $output .= htmlspecialchars($showInfo['first_air_date'] ?? '', ENT_QUOTES);
        $output .= ', ' . htmlspecialchars($showInfo['status'] ?? '', ENT_QUOTES);
        $output .= ', ' . htmlspecialchars($showInfo['number_of_seasons'] ?? '', ENT_QUOTES) . ' seasons</p>';
        $output .= '<p><em>' . htmlspecialchars($showInfo['tagline'] ?? '', ENT_QUOTES) . '</em></p>';
        $output .= '<p>' . mb_strimwidth($showInfo['overview'] ?? '', 0, 300, '&#8230;') . '</p>';

        $output .= '<ul class="cast">';
        if (!empty($credits['cast']) && is_array($credits['cast'])) {
            $i = 0;
            foreach ($credits['cast'] as $castMember) {
                $output .= '<li>' . htmlspecialchars($castMember['name'] ?? '', ENT_QUOTES) . '</li>';
                if (++$i === 7) break;
            }
        }
        $output .= '</ul>';

        $output .= '<ul class="genres">';
        if (!empty($showInfo['genres']) && is_array($showInfo['genres'])) {
            foreach ($showInfo['genres'] as $genre) {
                $output .= '<li>' . htmlspecialchars($genre['name'] ?? '', ENT_QUOTES) . '</li>';
            }
        }
        $output .= '</ul>';

        return $output . '</div></div>';
    }
}