<?php

namespace Uteq\Move\Tests\Feature;

use Illuminate\Http\Request;
use Uteq\Move\Rules\UniquePostcodeHousenumber;
use Uteq\Move\Tests\Fixtures\Contact;
use Uteq\Move\Tests\TestCase;

class RulesTest extends TestCase
{
    /** @test */
    public function unique_postcode_house_number_rule_works()
    {
        $rules = new UniquePostcodeHousenumber();

        $this->app->bind('request', function () {
            $request = new Request();
            $request->resource = 'contacts';
            $request->resourceId = 2;
            $request->merge([
                'house_number' => 2,
                'postcode' => '8607HZ',
            ]);

            return $request;
        });

        $this->assertTrue($rules->passes(true, true));

        Contact::factory([
            'house_number' => 2,
            'postcode' => '8607HZ',
        ])->make()->save();

        $this->assertFalse($rules->passes(true, true));
        $this->assertNotEmpty($rules->message());
    }
}
