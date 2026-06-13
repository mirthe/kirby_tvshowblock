<?php Kirby::plugin('mirthe/tvshowblock', [
    'options' => [
        'cache' => true
    ],
    'tags' => [
        'tvshowblock' => [
            'attr' => [
                'tmdb'
            ],
            'html' => function($tag) {
                $tmdbid = $tag->tmdb;
                $api_key = option('themoviedb.apiKey');

                $cache = kirby()->cache('mirthe.tvshowblock');
                $cacheKey = 'tmdb-tv-' . $tmdbid;
                $showData = $cache->get($cacheKey);

                if ($showData === null) {
                    $showUrl = "https://api.themoviedb.org/3/tv/" . $tmdbid . "?api_key=" . $api_key;
                    $ch = curl_init($showUrl);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_USERAGENT, kirby()->site()->title());
                    $rawShow = curl_exec($ch);
                    curl_close($ch);

                    $creditsUrl = "https://api.themoviedb.org/3/tv/" . $tmdbid . "/credits?api_key=" . $api_key;
                    $ch = curl_init($creditsUrl);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_USERAGENT, kirby()->site()->title());
                    $rawCredits = curl_exec($ch);
                    curl_close($ch);

                    $showData = [
                        'show'    => json_decode($rawShow, true),
                        'credits' => json_decode($rawCredits, true)
                    ];
                    $cache->set($cacheKey, $showData, 604800);
                }

                $movieinfo = $showData['show'] ?? null;
                $credits = $showData['credits'] ?? null;

                if (empty($movieinfo) || !is_array($movieinfo) || isset($movieinfo['status_code'])) {
                    return '<div class="well"><div class="well-body">TV-show niet gevonden</div></div>';
                }

                $poster = isset($movieinfo['poster_path']) ? 'https://www.themoviedb.org/t/p/w200/'.$movieinfo['poster_path'] : '';
                $mijnoutput = '<div class="well">';
                if ($poster !== '') {
                    $mijnoutput .= '<div class="well-img"><a href="https://www.themoviedb.org/tv/'.$movieinfo['id'].'"><img src="'.$poster.'" alt=""></a></div>';
                }
                $mijnoutput .= '<div class="well-body">';
                $mijnoutput .= '<p><a href="https://www.themoviedb.org/tv/'.$movieinfo['id'].'">'.htmlspecialchars($movieinfo['name'] ?? '', ENT_QUOTES).'</a><br>';
                $mijnoutput .= htmlspecialchars($movieinfo['first_air_date'] ?? '', ENT_QUOTES);
                $mijnoutput .= ', '.htmlspecialchars($movieinfo['status'] ?? '', ENT_QUOTES);
                $mijnoutput .= ', '.htmlspecialchars($movieinfo['number_of_seasons'] ?? '', ENT_QUOTES).' seasons</p>';
                $mijnoutput .= '<p><em>'.htmlspecialchars($movieinfo['tagline'] ?? '', ENT_QUOTES).'</em></p>';
                $mijnoutput .= '<p>'.mb_strimwidth($movieinfo['overview'] ?? '', 0, 300, '&#8230;').'</p>';

                $mijnoutput .= '<ul class="cast">';
                if (!empty($credits['cast']) && is_array($credits['cast'])) {
                    $i = 0;
                    foreach ($credits['cast'] as $genre) {
                        $mijnoutput .= '<li>'.htmlspecialchars($genre['name'] ?? '', ENT_QUOTES).'</li>';
                        if (++$i === 7) break;
                    }
                }
                $mijnoutput .= '</ul>';

                $mijnoutput .= '<ul class="genres">';
                if (!empty($movieinfo['genres']) && is_array($movieinfo['genres'])) {
                    foreach ($movieinfo['genres'] as $genre) {
                        $mijnoutput .= '<li>'.htmlspecialchars($genre['name'] ?? '', ENT_QUOTES).'</li>';
                    }
                }
                $mijnoutput .= '</ul>';

                $mijnoutput .= '</div></div>';
                return $mijnoutput;
            }
        ]
    ]
]);

