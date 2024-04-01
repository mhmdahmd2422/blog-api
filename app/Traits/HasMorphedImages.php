<?php

namespace App\Traits;

use App\Models\Image;
use Illuminate\Database\Eloquent\Builder;

trait HasMorphedImages
{
    public function whereHasImage(int $imageId): Image|null
    {
        return Image::whereHasMorph(
            'imageable',
            get_called_class(),
            function (Builder $query) {
                $query->whereId($this->id);
            }
        )->whereId($imageId)->first();
    }
}
