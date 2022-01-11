<?php

namespace Uteq\Move\Tests\Fixtures;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Uteq\Move\Tests\Factories\ContactFactory;

class Contact extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    /**
     * Form a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return new ContactFactory();
    }
}
