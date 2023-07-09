<?php

namespace App\Repositories;

abstract class Repository
{
    /**
     * Main repository model
     *
     * @var $model
     */
    protected $model;

    /**
     * Create a new repository instance.
     *
     * @param object $model Main repository model
     */
    public function __construct($model)
    {
        $this->model = $model;
    }

    /**
     * Get all resources in the storage.
     *
     * @return array object
     */
    public function all()
    {
        return $this->model->all();
    }

    /**
     * Get all resources in the storage using specified id.
     *
     * @return array object
     */
    public function allUsingSpecifiedId($id)
    {
        return $this->model->where('id', $id)->get();
    }

    /**
     * Get all resources with filters in the storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array object
     */
    public function allWithFilters($request = null)
    {
        return $this->model->filter($request)->get();
    }

    /**
     * Create pagination for the resources.
     *
     * @param  integer $length
     * @return array object
     */
    public function paginate($length = 10)
    {
        return $this->model->paginate($length);
    }

    /**
     * Create pagination with filters for the resources.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string                    $orderBy
     * @param  integer                   $length
     * @param  boolean                   $removePage
     * @return array object
     */
    public function paginateWithFilters(
        $request = null,
        $length = 15,
        $orderBy = 'desc',
        $removePage = true
    ) {
        if ($orderBy == null) {
            $orderBy = 'desc';
        }

        return $this->model->filter($request)
                            ->orderBy('created_at', $orderBy)
                            ->paginate($length)
                            ->withPath(
                                $this->model->createPaginationUrl($request, $removePage)
                            );
    }

    /**
     * Create pagination with filters for the resources including soft deleted resources.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  integer                   $length
     * @param  boolean                   $removePage
     * @param  string                    $orderBy
     * @return array object
     */
    public function paginateWithFiltersAndWithTrashed(
        $request = null,
        $length = 10,
        $removePage = true,
        $orderBy = 'desc'
    ) {
        return $this->model->filter($request)
            ->withTrashed()
            ->orderBy('created_at', $orderBy)
            ->paginate($length)
            ->withPath(
                $this->model->createPaginationUrl($request, $removePage)
            );
    }

    /**
     * Create pagination with filters and join users from the resources.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  integer                   $length
     * @param  boolean                   $removePage
     * @param  string                    $orderBy
     * @return array object
     */
    public function paginateWithFiltersAndUsers(
        $request = null,
        $length = 10,
        $removePage = true,
        $orderBy = 'desc'
    ) {
        return $this->model->filter($request)
            ->with('user')
            ->orderBy('created_at', $orderBy)
            ->paginate($length)
            ->withPath(
                $this->model->createPaginationUrl($request, $removePage)
            );
    }

    /**
     * Create pagination with filters for the resources including soft deleted resources.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  integer                   $length
     * @param  boolean                   $removePage
     * @param  string                    $orderBy
     * @return array object
     */
    public function paginateWithFiltersAndWithTrashedAndWithUsers(
        $request = null,
        $length = 10,
        $removePage = true,
        $orderBy = 'desc'
    ) {
        return $this->model->filter($request)
            ->withTrashed()
            ->with('user')
            ->orderBy('created_at', $orderBy)
            ->paginate($length)
            ->withPath(
                $this->model->createPaginationUrl($request, $removePage)
            );
    }

    /**
     * Find the resource using the specified id or else fail.
     *
     * @param  int $id
     * @return json object
     */
    public function findOrFail($id)
    {
        return $this->model->findOrFail($id);
    }

    /**
     * Find the resource using the specified id or else fail with user.
     *
     * @param  int $id
     * @return json object
     */
    public function findOrFailWithUser($id)
    {
        return $this->model->where('id', $id)
            ->with('admin')
            ->first();
    }

    /**
     * Find the resource using the specified slug.
     *
     * @param  string $id
     * @return json object
     */
    public function findBySlug($slug)
    {
        return $this->model->where('slug', $slug)->first();
    }

    /**
     * Find the resource using the specified slug or else fail with user.
     *
     * @param  string $id
     * @return json object
     */
    public function findBySlugWithUser($slug)
    {
        return $this->model->where('slug', $slug)->with('admin')->first();
    }

    /**
     * Get resources but limit it to a specified amount.
     *
     * @param  int $limit
     * @return array object
     */
    public function limit($limit)
    {
        return $this->model->limit($limit)->get();
    }

    /**
     * Store the data in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return boolean
     */
    public function store($request)
    {
        return $this->model->create($request->all());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return boolean
     */
    public function update($request, $id)
    {
        $model = $this->model->findOrFail($id);
        $model->fill($request->all());

        return $model->save();
    }

    /**
     * Remove the specified resource from the storage.
     *
     * @param  int $id
     * @return boolean
     */
    public function destroy($id)
    {
        return $this->model->destroy($id);
    }

    /**
     * Search the specified data from the storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return boolean
     */
    public function search($request = null)
    {
        if (count($request->all()) == 0) {
            return;
        }
        
        $limit = 15;

        if (isset($request->limit)) {
            $limit = $request->limit;
        }

        return $this->model->filter($request)->limit($limit)->get();
    }

    /**
     * Retrieve search url.
     *
     * @param  \Illuminate\Http\Request $request
     * @return string
     */
    public function getSearchUrl($request)
    {
        return $this->model->createPaginationUrl($request);
    }

    /**
     * Check if the user is authorize for certain ability.
     *
     * @param  string $ability
     * @return boolean
     */
    public function authorize($ability)
    {
        return auth()->user()->can($ability, $this->model);
    }

    /**
     * Retrieve archived resources for the model.
     *
     * @return array object
     */
    public function archives()
    {
        return $this->model->archives();
    }

    /**
     * Create pagination with filters for the resources.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  integer                   $length
     * @param  boolean                   $removePage
     * @param  string                    $orderBy
     * @return array object
     */
    public function paginateWithPublishedFilters(
        $request = null,
        $length = 10,
        $orderBy = 'desc',
        $removePage = true
    ) {
        return $this->model->filter($request)
            ->where('is_published', 1)
            ->orderBy('created_at', $orderBy)
            ->paginate($length)
            ->withPath(
                $this->model->createPaginationUrl($request, $removePage)
            );
    }

    public function restore($id)
    {
        $task = $this->model->onlyTrashed()->findOrFail($id);
        
        return $task->restore();
    }

    public function first()
    {        
        return $this->model->first();
    }
}
