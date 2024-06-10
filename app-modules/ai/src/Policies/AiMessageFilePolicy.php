<?php

namespace AdvisingApp\Ai\Policies;

use AdvisingApp\Ai\Models\AiMessageFile;
use App\Models\Authenticatable;
use Illuminate\Auth\Access\Response;

class AiMessageFilePolicy
{
    public function viewAny(Authenticatable $authenticatable): Response
    {
        //
    }

    public function view(Authenticatable $authenticatable, AiMessageFile $aiMessageFile): Response
    {
        //
    }

    public function create(Authenticatable $authenticatable): Response
    {
        //
    }

    public function update(Authenticatable $authenticatable, AiMessageFile $aiMessageFile): Response
    {
        //
    }

    public function delete(Authenticatable $authenticatable, AiMessageFile $aiMessageFile): Response
    {
        //
    }

    public function restore(Authenticatable $authenticatable, AiMessageFile $aiMessageFile): Response
    {
        //
    }

    public function forceDelete(Authenticatable $authenticatable, AiMessageFile $aiMessageFile): Response
    {
        //
    }
}
