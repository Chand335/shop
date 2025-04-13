<?php

namespace App\Filament\Resources\PostTagResource\Pages;

use App\Filament\Resources\PostTagResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePostTag extends CreateRecord
{
    protected static string $resource = PostTagResource::class;
}
