<?php

namespace PHPSTORM_META {

    // Reflect
    use Psr\Http\Message\ServerRequestInterface;

    override(\Psr\Container\ContainerInterface::get(0), map('@'));
    override(\Hyperf\Context\Context::get(0), map('@'));
    override(\make(0), map('@'));
    override(\di(0), map('@'));
    override(ServerRequestInterface::getAttribute(), map('@'));
}