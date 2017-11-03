<?php namespace App\Lib\SortFilter;
use Exception;
use Slim\Http\Request;

class SortFilter
{
    /**
     * Return a new Result object for a single dataset
     *
     * @param  mixed                           $queryBuilder   Some kind of query builder instance
     * @param  array|integer                   $identification Identification of the dataset to work with
     * @param  array|boolean                   $queryParams    The parameters used for parsing
     * @return Marcelgwerder\ApiHandler\Result                 Result object that provides getter methods
     */
    public function parseSingle(Request $request,$queryBuilder, $identification, $queryParams = false)
    {

        if ($queryParams === false) {
            $queryParams = $request->getQueryParams();
        }

        $parser = new Parser($queryBuilder, $queryParams);
        $parser->parse($identification);

        return new Result($parser);
    }

    /**
     * Return a new Result object for multiple datasets
     *
     * @param  mixed            $queryBuilder          Some kind of query builder instance
     * @param  array            $fullTextSearchColumns Columns to search in fulltext search
     * @param  array|boolean    $queryParams           A list of query parameter
     * @return Result
     */
    public function parseMultiple(Request $request,$queryBuilder, $fullTextSearchColumns = array(), $queryParams = false)
    {
        if ($queryParams === false) {
            $queryParams = $request->getQueryParams();
        }

        $parser = new Parser($queryBuilder, $queryParams);
        $parser->parse($fullTextSearchColumns, true);

        return new Result($parser);
    }
}
