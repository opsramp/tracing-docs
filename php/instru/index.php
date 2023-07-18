<?php

// Require the composer autoload file
require 'vendor/autoload.php';

// Import the necessary classes
use OpenTelemetry\API\Common\Instrumentation\CachedInstrumentation;
use OpenTelemetry\API\Trace\Span;
use OpenTelemetry\API\Trace\StatusCode;
use OpenTelemetry\Context\Context;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

// Create a Slim app
$app = AppFactory::create();

// Create an instrumentation instance
$instrumentation = new CachedInstrumentation('example');

// Define a route for the /hello endpoint
$app->get('/hello', function (Request $request, Response $response) use ($instrumentation) {
    // Start a span for the route handler
    $span = $instrumentation->tracer()->spanBuilder('hello-handler')->startSpan();
    // Attach the span to the current context
    Context::storage()->attach($span->storeInContext(Context::getCurrent()));

    // Do some work and write the response
    $name = $request->getQueryParams()['name'] ?? 'world';
    $response->getBody()->write("Hello, $name!");
	
    $span->setAttribute("http.method", "GET");
    $span->setAttribute("http.url", "Vamsi Gandhi");
    // End the span and detach it from the context
    $span->end();
    Context::storage()->scope()->detach();

    // Return the response
    return $response;
});

// Run the app
$app->run();

