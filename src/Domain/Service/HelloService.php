<?php

namespace TVListings\Domain\Service;

class HelloService
{
    /**
     * @return string
     */
    public function sayHello($name = "World")
    {
        return sprintf("Hello %s!", $name);
    }
}
