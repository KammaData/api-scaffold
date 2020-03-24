<?php

declare(strict_types=1);

namespace KammaData\ApiScaffold\Interfaces;

use KammaData\ApiScaffold\Interfaces\ErrorHandlerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;

interface ShutdownHandlerInterface {
    public function __construct(Request $request, ErrorHandlerInterface $errorHandler, bool $displayErrorDetails);
    public function __invoke();
};

?>
