<?php

namespace App\Console\Commands;

use App\Rest\OpenApi;
use Lomkit\Rest\Console\Commands\DocumentationCommand as BaseDocumentationCommand;

class DocumentationCommand extends BaseDocumentationCommand
{
    // TODO: We can delete this when ___ is merged in
    public function handle()
    {
        $openApi = (new OpenApi())
            ->generate();

        $path = $this->getPath('openapi');

        $this->makeDirectory($path);

        $this->files->put(
            $path,
            json_encode($openApi->jsonSerialize())
        );

        $this->info('The documentation was generated successfully!');
        $this->info('Open ' . url(config('rest.documentation.routing.path')) . ' in a web browser.');
    }
}
