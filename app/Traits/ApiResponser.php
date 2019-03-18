<?php

namespace App\Traits;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;

trait ApiResponser
{
    public function successResponse($data, $code)
    {
        return response()->json($data, $code);
    }

    protected function errorResponse($message, $code) {
        return response()->json(['error' => $message, 'code' => $code], $code);
    }

    protected function showAll(Collection $collection, $code = 200)
    {
        $collection = $this->filterData($collection);
        $collection = $this->sortData($collection);
        $collection = $this->paginate($collection);
        // $collection = $this->cacheResponse($collection);

        return $this->successResponse($collection, $code);
    }

    protected function showOne(Model $model, $code = 200)
    {
        return $this->successResponse(['data' => $model], $code);
    }

    protected function showMessage($message, $code = 200)
    {
        return $this->successResponse(['data' => $message], $code);
    }

    protected function filterData(Collection $collection)
    {
        foreach (request()->query() as $query => $value) {
            $exception = ['force', 'sort_by', 'per_page', 'page'];
            if (isset($query, $value) && !in_array($query, $exception)) {
                $collection = $collection->where($query, $value);
            }
        }

        return $collection;
    }

    protected function sortData(Collection $collection)
    {
        if (request()->has('sort_by')) {
            $attribute = request()->sort_by;
            $collection = $collection->sortBy->{$attribute}->values()->all();
            $collection = collect($collection);
        }

        return $collection;
    }

    protected function paginate(Collection $collection)
    {
        Validator::validate(request()->all(), [
            'per_page' => 'integer|min:2|max:50'
        ]);

        $page = LengthAwarePaginator::resolveCurrentPage();
        $perPage = request()->has('per_page') ? (int) request()->per_page : 15;
        $results = $collection->slice(($page - 1) * $perPage, $perPage)->values();

        $paginated = new LengthAwarePaginator($results, $collection->count(), $perPage, $page, [
            'path' => LengthAwarePaginator::resolveCurrentPath()
        ]);
        $paginated->appends(request()->all());

        return $paginated;
    }

    protected function cacheResponse($data)
    {
        $url = request()->url();
        $queryParams = request()->query();
        ksort($queryParams);

        $queryString = http_build_query($queryParams);
        $fullURL = "{$url}?{$queryString}";

        return Cache::remember($fullURL, 30/60, function() use($data) {
            return $data;
        });
    }
}
