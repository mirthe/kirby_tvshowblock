# Kirby Plugin: TV Show Block

This plugin allows you to show information for a tv show from the TheMovieDB API. 
Though that might change to a different service at some later point

## Git submodule

```
git submodule add https://github.com/mirthe/kirby_tvshowblock site/plugins/tvshowblock
```

## Usage

You'll need an API key for this from 
https://developers.themoviedb.org/3/getting-started/authentication

Add the following to your config where XX is your key:

    'themoviedb.apiKey' => 'XX'

## Example 

Placed for example with 

    (tvshowblock: tmdb: 185)

<img src="https://github.com/mirthe/kirby_tvshowblock/blob/18889e96393b95a7a14ebd7b4ffccf24e5f6b2c3/example.png" alt="Example of usage">

## Example CSS

See https://css-tricks.com/how-to-make-a-media-query-less-card-component/

## Todo

- Offer as an official Kirby plugin
- Might use other service(s)
- Add sample SCSS to this readme
- Cleanup code
- Lots..
