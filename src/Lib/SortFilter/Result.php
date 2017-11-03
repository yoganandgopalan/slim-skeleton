<?php namespace App\Lib\SortFilter;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Response;
use \Illuminate\Support\Facades\Config;

class Result
{
    /**
     * Parser instance.
     *
     * @var Marcelgwerder\SortFilter\Parser
     */
    protected $parser;

    /**
     * If true, the result will get cleaned up from unintentionally added relations.
     *
     * @var null|bool
     */
    private $cleanup = null;

    /**
     * Create a new result
     *
     * @param  Marcelgwerder\SortFilter\Parser $parse
     * @return void
     */
    public function __construct(Parser $parser)
    {
        $this->parser = $parser;
    }

    /**
     * Return a laravel response object including the correct status code and headers
     *
     * @param bool $resultOrFail
     * @return Illuminate\Support\Facades\Response
     */
    public function getResponse($resultOrFail = false)
    {
        $headers = $this->getHeaders();

        // if the cleanup flag is not explicitly set, get the default from the config
        if ($this->cleanup === null) {
            $this->cleanup('false');
        }

        if ($resultOrFail) {
            $result = $this->getResultOrFail();
        } else {
            $result = $this->getResult();
        }

        if ($this->parser->mode == 'count') {
            //return Response::json($headers, 200, $headers);
	    return $headers;
        } else {
            if ($this->parser->envelope) {
                /*return Response::json([
                    'meta' => $headers,
                    'data' => $result,
                ], 200); */
		return $result;
            } else {
                //return Response::json($result, 200, $headers);
		return $result;
            }

        }
    }

    /**
     * Return the query builder including the results
     *
     * @return Illuminate\Database\Query\Builder $result
     */
    public function getResult()
    {
        if ($this->parser->multiple) {
            $result = $this->parser->builder->get();

            if ($this->cleanup) {
                $result = $this->cleanupRelationsOnModels($result);
            }
        } else {
            $result = $this->parser->builder->first();

            if ($this->cleanup) {
                $result = $this->cleanupRelations($result);
            }
        }

        return $result;
    }

    /**
     * Return the query builder including the result or fail if it could not be found
     *
     * @return Illuminate\Database\Query\Builder $result
     */
    public function getResultOrFail()
    {
        if ($this->parser->multiple) {
            return $this->getResult();
        }

        $result = $this->parser->builder->firstOrFail();

        if ($this->cleanup) {
            $result = $this->cleanupRelations($result);
        }

        return $result;
    }

    /**
     * Get the query bulder object
     *
     * @return Illuminate\Database\Query\Builder
     */
    public function getBuilder()
    {
        return $this->parser->builder;
    }

    /**
     * Get the headers
     *
     * @return array
     */
    public function getHeaders()
    {
        $meta = $this->parser->meta;
        $headers = [];

        foreach ($meta as $provider) {
            if ($this->parser->envelope) {
                $headers[strtolower(str_replace('-', '_', preg_replace('/^Meta-/', '', $provider->getTitle())))] = $provider->get();
            } else {
                $headers[$provider->getTitle()] = $provider->get();
            }
        }

        return $headers;
    }

    /**
     * Get an array of meta providers
     *
     * @return array
     */
    public function getMetaProviders()
    {
        return $this->parser->meta;
    }

    /**
     * Get the mode of the parser
     *
     * @return string
     */
    public function getMode()
    {
        return $this->parser->mode;
    }

    /**
     * Set the cleanup flag
     *
     * @param $cleanup
     * @return $this
     */
    public function cleanup($cleanup)
    {
        $this->cleanup = $cleanup;

        return $this;
    }

    /**
     * Cleanup the relations on a models array
     *
     * @param $models
     * @return array
     */
    public function cleanupRelationsOnModels($models)
    {
        $response = [];

        if ($models instanceof Collection) {
            foreach ($models as $model) {
                $response[] = $this->cleanupRelations($model);
            }
        }

        return $response;
    }

    /**
     * Cleanup the relations on a single model
     *
     * @param $model
     * @return mixed
     */
    public function cleanupRelations($model)
    {
        if (!($model instanceof Model)) {
            return $model;
        }

        // get the relations which already exists on the model (e.g. with $builder->with())
        $allowedRelations = array_fill_keys($this->getRelationsRecursively($model), true);

        // parse the model to an array and get the relations which got added unintentionally
        // (e.g. when accessing a relation in an accessor method or somewhere else)
        $response = $model->toArray();
        $loadedRelations = array_fill_keys($this->getRelationsRecursively($model), true);

        // remove the unintentionally added relations from the response
        return $this->removeUnallowedRelationsFromResponse($response, $allowedRelations, $loadedRelations);
    }

    /**
     * Get all currently loaded relations on a model recursively
     *
     * @param $model
     * @param null $prefix
     * @return array
     */
    protected function getRelationsRecursively($model, $prefix = null)
    {
        $loadedRelations = $model->getRelations();
        $relations = [];

        foreach ($loadedRelations as $key => $relation) {
            $relations[] = ($prefix ?: '') . $key;
            $relationModel = $model->{$key};

            // if the relation is a collection, just use the first element as all elements of a relation collection are from the same model
            if ($relationModel instanceof Collection) {
                if (count($relationModel) > 0) {
                    $relationModel = $relationModel[0];
                } else {
                    continue;
                }
            }

            // get the relations of the child model
            if ($relationModel instanceof Model) {
                $relations = array_merge($relations, $this->getRelationsRecursively($relationModel, ($prefix ?: '') . $key . '.'));
            }
        }

        return $relations;
    }

    /**
     * Remove all relations which are in the $loadedRelations but not in $allowedRelations from the model array
     *
     * @param $response
     * @param $allowedRelations
     * @param $loadedRelations
     * @param null $prefix
     * @return mixed
     */
    protected function removeUnallowedRelationsFromResponse($response, $allowedRelations, $loadedRelations, $prefix = null)
    {
        foreach ($response as $key => $attr) {
            $relationKey = ($prefix ?: '') . $key;

            // handle associative arrays as they
            if (isset($loadedRelations[$relationKey])) {
                if (!isset($allowedRelations[$relationKey])) {
                    unset($response[$key]);
                } else if (is_array($attr)) {
                    $response[$key] = $this->removeUnallowedRelationsFromResponse($response[$key], $allowedRelations, $loadedRelations, ($prefix ?: '') . $relationKey . '.');
                }

            // just pass numeric arrays to the method again as they may contain additional relations in their values
            } else if (is_array($attr) && is_numeric($key)) {
                $response[$key] = $this->removeUnallowedRelationsFromResponse($response[$key], $allowedRelations, $loadedRelations, $prefix);
            }
        }

        return $response;
    }

    /**
     * Convert the result to its string representation.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getResponse()->__toString();
    }
}
