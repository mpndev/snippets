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

}
