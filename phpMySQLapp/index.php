<?php
use OpenTelemetry\API\Common\Instrumentation\Globals;
use OpenTelemetry\API\Logs\EventLogger;
use OpenTelemetry\API\Common\Signal\Signals;
use OpenTelemetry\Contrib\Grpc\GrpcTransportFactory;
use OpenTelemetry\Contrib\Otlp\OtlpUtil;
use OpenTelemetry\API\Logs\LogRecord;
use OpenTelemetry\API\Trace\Propagation\TraceContextPropagator;
use OpenTelemetry\Contrib\Otlp\LogsExporter;
use OpenTelemetry\Contrib\Otlp\MetricExporter;
use OpenTelemetry\Contrib\Otlp\SpanExporter;
use OpenTelemetry\SDK\Common\Attribute\Attributes;
use OpenTelemetry\SDK\Common\Export\Stream\StreamTransportFactory;
use OpenTelemetry\SDK\Logs\LoggerProvider;
use OpenTelemetry\SDK\Logs\Processor\SimpleLogsProcessor;
use OpenTelemetry\SDK\Metrics\MeterProvider;
use OpenTelemetry\SDK\Metrics\MetricReader\ExportingReader;
use OpenTelemetry\SDK\Resource\ResourceInfo;
use OpenTelemetry\SDK\Resource\ResourceInfoFactory;
use OpenTelemetry\SDK\Sdk;
use OpenTelemetry\SDK\Trace\Sampler\AlwaysOnSampler;
use OpenTelemetry\SDK\Trace\Sampler\ParentBased;
use OpenTelemetry\SDK\Trace\SpanProcessor\SimpleSpanProcessor;
use OpenTelemetry\SDK\Trace\TracerProvider;
use OpenTelemetry\SemConv\ResourceAttributes;


require 'vendor/autoload.php';

$resource = ResourceInfoFactory::emptyResource()->merge(ResourceInfo::create(Attributes::create([
    ResourceAttributes::SERVICE_NAMESPACE => 'demo',
    ResourceAttributes::SERVICE_NAME => 'test-application',
    ResourceAttributes::SERVICE_VERSION => '0.1',
    ResourceAttributes::DEPLOYMENT_ENVIRONMENT => 'development',
])));
#$spanExporter = new SpanExporter(
#    (new StreamTransportFactory())->create('php://stdout', 'application/json')
#);

#$transport = (new OtlpHttpTransportFactory())->create('http://0.0.0.0:8082', 'application/x-protobuf');
$transport = (new GrpcTransportFactory())->create('http://0.0.0.0:9090' . OtlpUtil::method(Signals::TRACE));
$spanExporter = new SpanExporter($transport);


$tracerProvider = TracerProvider::builder()
    ->addSpanProcessor(
        new SimpleSpanProcessor($spanExporter)
    )
    ->setResource($resource)
    ->setSampler(new ParentBased(new AlwaysOnSampler()))
    ->build();


Sdk::builder()
    ->setTracerProvider($tracerProvider)
    ->setPropagator(TraceContextPropagator::getInstance())
    ->setAutoShutdown(true)
    ->buildAndRegisterGlobal();


#$tracerProvider = Globals::tracerProvider();
$tracer = $tracerProvider->getTracer(
  'otel', //name (required)
  '1.0.0', //version
  '/index.php', //schema url
  ['route' => 'books'] //attributes
);

$span = $tracer->spanBuilder("get_result")->startSpan();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Movie DataBase</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.2.1.js"></script>


</head>

<body>
<nav class="navbar navbar-default">
    <div class="container-fluid">
        <div class="navbar-header">
            <a class="navbar-brand" href="#">My Website</a>
        </div>
        <ul class="nav navbar-nav">
            <li class="active"><a href="#">Home</a></li>
            <li><a href="./books/home.php">Books</a></li>
            <li><a href="./movies/home.php">Movies</a></li>
        </ul>
    </div>
</nav>

<div class="container">
    <h2>What should I do??</h2>
    <div class="well well-lg">Thinking...</div>

    <div class="row">
        <div class="col-md-6">

            <a href="./books/home.php">
                <img src="./siteImages/books.jpg" class="img-circle" alt="books" width="400" height="400">
                <div class="caption">
                    <p>Study Time...</p>
                </div>
            </a>

        </div>
        <div class="col-md-6">

            <a href="./movies/home.php">
                <img src="./siteImages/movies.jpg" class="img-circle" alt="movies" width="400" height="400">
                <div class="caption">
                    <p>Movie Time...</p>
                </div>
            </a>

        </div>
    </div>
</div>

</body>
</html>


<?php  $span->end(); ?>

