<?php Kirby::plugin('mirthe/tvshowblock', [
    'options' => [
        'cache' => true
    ],
    'tags' => [
        'tvshowblock' => [
            'attr' =>[
                'tmdb'
            ],
            'html' => function($tag) {

                $tmdbid = $tag->tmdb;
                $api_key = option('themoviedb.apiKey');
               
                $url = "https://api.themoviedb.org/3/tv/". $tmdbid ."?api_key=" . $api_key;
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                $rawdata = curl_exec($ch);
                curl_close($ch);
                $movieinfo = json_decode($rawdata,true);
                // print_r($movieinfo); exit(); 

                $url = "https://api.themoviedb.org/3/tv/". $tmdbid ."/credits?api_key=" . $api_key;
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                $rawdata_credits = curl_exec($ch);
                curl_close($ch);
                $credits = json_decode($rawdata_credits,true);
                
                $mijnoutput = '<div class="well">';
                $mijnoutput .= '<div class="well-img"><img src="https://www.themoviedb.org/t/p/w200/'.$movieinfo['poster_path'].'" alt=""></div>';
                // show first season poster, if available. might add this as an option..
                // $mijnoutput .= '<div class="well-img"><img src="https://www.themoviedb.org/t/p/w200/'.$movieinfo['seasons'][0]['poster_path'].'" alt=""></div>';
                $mijnoutput .= '<div class="well-body">';
                $mijnoutput .= '<p><a href="https://www.themoviedb.org/tv/'.$movieinfo['id'].'">'.$movieinfo['name']."</a><br>
                ". $movieinfo['first_air_date'].", ".$movieinfo['status'].", ".$movieinfo['number_of_seasons']." seasons</p>";
                $mijnoutput .= '<p><em>'.$movieinfo['tagline']."</em></p>";
                $mijnoutput .= '<p>'.mb_strimwidth($movieinfo['overview'],0,300, '&#8230;')."</p>";

                $i = 0;
                $mijnoutput .= "<ul class=\"cast\">";
                foreach ($credits['cast'] as $genre) {
                    $mijnoutput .= '<li>'. $genre['name'] . "</li>";
                    if (++$i == 7) break;
                }
                $mijnoutput .= "</ul>";

                $mijnoutput .= "<ul class=\"genres\">";
                foreach ($movieinfo['genres'] as $genre) {
                    $mijnoutput .= '<li>'. $genre['name'] . "</li>";
                }
                $mijnoutput .= "</ul>";

                $mijnoutput .= '</div></div>';
               
                return $mijnoutput;
            }
        ]
    ]
]);

?>