<?php

/*
 * This file is part of laravel filtering package.
 *
 * (c) Gether Kestrel B. Medel <gether.medel@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/*
|--------------------------------------------------------------------------
| Auto filtering with pagination for laravel models
|--------------------------------------------------------------------------
|
| Here is where all the filtering logic happens, from filtering the
| current model to filtering relationships and entrust trait.
|
*/

namespace App\Traits;

use Illuminate\Support\Carbon;

trait Filtering
{
    /**
     * Retrieve archives for model.
     *
     * @return array
     */
    public static function scopeArchives($query, $request)
    {
        $query->selectRaw('year(created_at) year, monthname(created_at) month, count(*) published')
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc');

        if ($month = $request->month) {
            $query->whereMonth('created_at', Carbon::parse($month)->month);
        }

        if ($year = $request->year) {
            $query->whereYear('created_at', $year);
        }

        return $query->get();
    }

    /**
     * Filter the query for model.
     *
     * 1. Filter by month and year
     * 2. Filter by other queries
     *
     * 3-a. Check if searchColumn exists in queries
     * 3-b. If searchColumn exists in the string then remove it
     * 3-c. Get all uppercase letter and their position
     * 3-d. $matches comes from the $matches in preg_match_all
     * 3-e. Loop everything in $matches
     * 3-f. Then add '_' to every upper case and convert it to lowercase
     * 3-g. Remove the first '_' in the string then add it to query
     *
     * @param  object $query
     * @param  \Illimunate\Http\Request $request
     * @return void
     */
    public function scopeFilter($query, $request)
    {
        $query->where(function ($query) use ($request) {
            foreach ($request->all() as $key => $value) {
                $column = self::convertToColumn($key);

                if (in_array($column, $this->fillable) && $value != null) {
                    if ((strpos($key, 'FromModel') == false &&
                        strrpos($key, 'Model') == false &&
                        strpos($key, 'from_') == false &&
                        strpos($key, 'from_model_') == false) &&
                        ! is_array($value)
                    ) {
                        if (self::checkIfQueryShouldBeStrict($request->is_strict)) {
                            $query->where($column, 'Like', '%' . $value . '%');
                        } else {
                            $query->orWhere($column, 'Like', '%' . $value . '%');
                        }
                    }

                    if ((strpos($key, 'FromModel') == false &&
                        strrpos($key, 'Model') == false &&
                        strpos($key, 'from_') == false &&
                        strpos($key, 'from_model_') == false) &&
                        is_array($value)
                    ) {
                        if (self::checkIfQueryShouldBeStrict($request->is_strict)) {
                            $query->where(function ($query) use ($key, $value) {
                                foreach ($value as $arrayValue) {
                                    if ($arrayValue !== null) {
                                        $query->orWhere($column, 'Like', '%' . $arrayValue . '%');
                                    }
                                }
                            });
                        } else {
                            $query->orWhere(function ($query) use ($key, $value) {
                                foreach ($value as $arrayValue) {
                                    if ($arrayValue !== null) {
                                        $query->orWhere($column, 'Like', '%' . $arrayValue . '%');
                                    }
                                }
                            });
                        }
                    }
                }
            }
        });

        self::checkTraits($query, $request);
    }

    /**
     * Check model if it uses other filtering traits.
     *
     * @param  object $query
     * @param  object $request
     * @return void
     */
    public function checkTraits($query, $request)
    {
        foreach (class_uses($this) as $trait) {
            if (strpos($trait, 'FilterRelationships') !== false) {
                $this->filterRelationships($query, $request);
            }

            if (strpos($trait, 'FilterEntrust') !== false) {
                $this->filterEntrust($query, $request);
            }
        }
    }

    /**
     * Check if query should be strict.
     *
     * @param  boolean $isStrict
     * @return boolean
     */
    public function checkIfQueryShouldBeStrict($isStrict = false)
    {
        return ($isStrict == "true") ? true : false;
    }

    /**
     * Convert key string to column name.
     *
     * @param  string $key
     * @return string
     */
    public function convertToColumn($key)
    {
        $column = preg_replace('/searchColumn|searchArrayColumn|searchArray|search|FromModel.+|Model.+|from_model_.+|from_.+/i', '', $key);

        preg_match_all('/[A-Z]/', $column, $matches, PREG_OFFSET_CAPTURE);

        foreach ($matches[0] as $key => $matchPair) {
            $column = str_replace($matchPair[0], '_' . lcfirst($matchPair[0]), $column);
        }

        if (strpos($column, '_') !== false && preg_match('/\_$/', $column)) {
            $column = preg_replace('/\_$/', '', $column, 1);
        }

        return $column;
    }

    /**
     * Check and clean url also remove duplicated key value pairs.
     *
     * 1. Check if request uri has queries
     * 2. Parse all queries to remove duplication
     * 3. Remove _method and _token key
     * 4. Create full uri by combining current uri and query
     * 5. If remove page is true return full uri and page key
     * 6. Replace ?& with ? at the start of the query
     * 7. Remove & at the end of the query
     * 8. !! Return with page if $removePage is false
     * 9. !! Just return the path if no query found in the url
     *
     * @param  \Illuminate\Http\Request &$request
     * @return string
     */
    public static function createPaginationUrl($request, $removePage = false)
    {
        if (parse_url($request->path() . '?' . http_build_query(($request->all())), PHP_URL_QUERY)) {
            parse_str(parse_url($request->path() . '?' . http_build_query(($request->all())), PHP_URL_QUERY), $output);

            unset($output['_method']);
            unset($output['_token']);

            $uri = $request->path() . '?' . http_build_query($output);

            if ($removePage) {
                return preg_replace('/\?page=[\d]{1,}/', '', '/' . preg_replace('/\?&/', '?', rtrim(preg_replace('/\?page=[\d]{1,}\&page=[\d]{1,/', '', $uri), '&')));
            }

            return '/' . preg_replace('/\?&/', '?', rtrim($uri, '&'));
        }

        return '/' . $request->path();
    }
}
