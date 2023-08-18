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


session_start ();
require '../vendor/autoload.php';

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


$tracer = $tracerProvider->getTracer(
  'otel', //name (required)
  '1.0.0', //version
  '/books/home.php', //schema url
  ['route' => 'books'] //attributes
);

// Declare a global count variable and initialize it to zero if not set
global $count;
if (!isset ($_SESSION ['count'])) {
  $_SESSION ['count'] = 0;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Book DataBase</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.2.1.js"></script>
    <script>
        function showBook(str) {
            if (str.length == 0) {
                document.getElementById("data").innerHTML = "";
                return;
            } else {
                var xmlhttp = new XMLHttpRequest();
                xmlhttp.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                        document.getElementById("data").innerHTML = this.responseText;
                    }
                };
                xmlhttp.open("GET", "functions/fetch.php?search=" + str, true);
                xmlhttp.send();
            }
        }
    </script>
</head>
<body>

<nav class="navbar navbar-default">
    <div class="container-fluid">
        <div class="navbar-header">
            <a class="navbar-brand" href="#">My Website</a>
        </div>
        <ul class="nav navbar-nav">
            <li ><a href="../index.php">Home</a></li>
            <li class="active"><a href="./home.php">Books</a></li>
            <li ><a href="../movies/home.php">Movies</a></li>
        </ul>
    </div>
</nav>

<?php

include("./includes/bookDatabase.php");
include("./functions/functions.php");
include("./functions/getbook.php");

function get_result($tracer){

    $_SESSION ['count']++;
    $parent = $tracer->spanBuilder("parent")->startSpan();
    $parent->setAttribute("api hitted", $_SESSION ['count']);

    if (isset($_GET['cats'])){
        $result = $_GET['cats'];
        //echo $q;
        get_films($result);
    }
    if (isset($_GET['country'])){
        $result = $_GET['country'];
        //echo $q;
        get_country($result);
    }
    if (isset($_GET['author'])){
        $result = $_GET['author'];
        //echo $q;
        get_author($result);
    }
    if (isset($_GET['year'])){
        $result = $_GET['year'];
        //echo $q;
        get_year($result);
    }
    $scope = $parent->activate();
    try {
      $child = $tracer->spanBuilder("child")->startSpan();
      $child->end();
    } finally {
      $parent->end();
      $scope->detach();
    }
}
?>

<div class="container">
    <div class="jumbotron" style="background-image: url('./images/background_image.jpg')">
        <h1 style="color: lightcyan">My Books</h1>
    </div>
</div>
<div class="container">
    <ul class="nav nav-pills">
        <li class="active"><a href="home.php">Book Collection</a></li>
        <li class="dropdown">
            <a class="dropdown-toggle" data-toggle="dropdown" href="#">By Categories
                <span class="caret"></span></a>
                <ul class="dropdown-menu" name="users" onchange="showUser(this.value)">
                    <?php
                    getcategories();
                    ?>
                </ul>
        </li>


        <li class="dropdown">
            <a class="dropdown-toggle" data-toggle="dropdown" href="#">By Authors
                <span class="caret"></span></a>
            <ul class="dropdown-menu">
                <?php
                getAuthors();
                ?>
            </ul>
        </li>

        <li class="dropdown">
            <a class="dropdown-toggle" data-toggle="dropdown" href="#">By Years
                <span class="caret"></span></a>
            <ul class="dropdown-menu">
                <?php
                getYears();
                ?>
            </ul>
        </li>

        <li class="dropdown">
            <a class="dropdown-toggle" data-toggle="dropdown" href="#">By Countries
                <span class="caret"></span></a>
            <ul class="dropdown-menu">
                <?php
                getCountries();
                ?>
            </ul>
        </li>

        <li><a href="../admin_area/insert_books.php">Add Book</a></li>
    </ul>
    <br>
    <div class="form-group">
        <div class="input-group">
            <span class="input-group-addon">Search</span>
            <input type="text" name="searchText" class="form-control" id="searchText"
                   placeholder="Enter Book Name" onkeyup="showBook(this.value)">

        </div>
        <p> <span id="data"></span></p>
        <br>

    </div>

    <div class="panel-group">
        <div class="panel panel-info">
            <div class="panel-heading"><b>Books</b></div>
            <div class="panel-body"><b>Books will be listed here...</b></div>
            <div id="result"><?php get_result($tracer)?></div>
        </div>

    </div>
</div>



</body>
</html>


