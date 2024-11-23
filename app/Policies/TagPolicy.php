<?php

namespace App\Policies;

use App\Models\Authenticatable;
use App\Models\Tag;
use Illuminate\Auth\Access\Response;

class TagPolicy
{
    public function viewAny(Authenticatable $authenticatable): Response
    {
        //
    }

    public function view(Authenticatable $authenticatable, Tag $tag): Response
    {
        //
    }

    public function create(Authenticatable $authenticatable): Response
    {
        //
    }

    public function update(Authenticatable $authenticatable, Tag $tag): Response
    {
        //
    }

    public function delete(Authenticatable $authenticatable, Tag $tag): Response
    {
        //
    }

    public function restore(Authenticatable $authenticatable, Tag $tag): Response
    {
        //
    }

    public function forceDelete(Authenticatable $authenticatable, Tag $tag): Response
    {
        //
    }
}
