<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\TestResponse;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected $test_route_name = '/';

    protected $test_verb = 'GET';

    /**
     * Make json request
     * @return TestResponse
     */
    protected function apiRequest($route_bindings = [], $data = [])
    {
        if ($this->test_verb === 'get' && count($data)) {
            $this->fail("GET request cannot have body! Check your test implementation!");
        }
        return $this->json($this->test_verb, route($this->test_route_name, $route_bindings), $data);
    }

    /**
     * Make http request
     * @return TestResponse
     */
    protected function request($route_bindings = [], $data = [])
    {
        if ($this->test_verb === 'get' && count($data)) {
            $this->fail("GET request cannot have body! Check your test implementation!");
        }
        $verb = $this->test_verb;
        return $this->{$verb}(route($this->test_route_name, $route_bindings), $data);
    }

    /**
     * @param string $string_needle
     * @param $items
     */
    protected function assertArrayContainsRecursive(string $string_needle, $items)
    {
        $exists = false;
        $this->inArrayRecursive($string_needle, $items, $exists);
        $this->assertTrue($exists);
    }

    /**
     * @param $needle
     * @param $items
     * @param $exists
     */
    protected function inArrayRecursive($needle, $items, &$exists) {
        if (is_array($items)) {
            if (in_array($needle, $items, true)) {
                $exists = true;
                return;
            }
            foreach ($items as $item) {
                $this->inArrayRecursive($needle, $item, $exists);
            }
        }
    }

}
