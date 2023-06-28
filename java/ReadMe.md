# Java

## Auto Instrumentation

### Setup

1. Download opentelemetry-javaagent.jar from [Releases](https://github.com/open-telemetry/opentelemetry-java-instrumentation/releases) of the opentelemetry-java-instrumentation repo and place the JAR in your preferred directory. The JAR file contains the agent and instrumentation libraries.

2. Add -javaagent:path/to/opentelemetry-javaagent.jar and other config to your JVM startup arguments and launch your app:

    Directly on the startup command:

    ```bash
    java -javaagent:path/to/opentelemetry-javaagent.jar -Dotel.service.name=your-service-name -jar myapp.jar
    ```

    Via the JAVA_TOOL_OPTIONS and other environment variables:

    ```bash
    export JAVA_TOOL_OPTIONS="-javaagent:path/to/opentelemetry-javaagent.jar"
    export OTEL_SERVICE_NAME="your-service-name"
    java -jar myapp.jar
    ```

Supported Libraries: <https://github.com/open-telemetry/opentelemetry-java-instrumentation/blob/main/docs/supported-libraries.md>

### Agent Configuration

Reference Link: <https://opentelemetry.io/docs/instrumentation/java/automatic/agent-config/>

| System property                                          | Environment variable                                     | Description                                                                                                                                                                                                                                                                                                                                                                                                          |
|----------------------------------------------------------|----------------------------------------------------------|----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| otel.traces.exporter=otlp (default)                      | OTEL_TRACES_EXPORTER=otlp                                | Select the OpenTelemetry exporter for tracing (default)                                                                                                                                                                                                                                                                                                                                                              |
| otel.metrics.exporter=otlp (default)                     | OTEL_METRICS_EXPORTER=otlp                               | Select the OpenTelemetry exporter for metrics (default)                                                                                                                                                                                                                                                                                                                                                              |
| otel.logs.exporter=otlp (default)                        | OTEL_LOGS_EXPORTER=otlp                                  | Select the OpenTelemetry exporter for logs (default)                                                                                                                                                                                                                                                                                                                                                                 |
| otel.exporter.otlp.endpoint                              | OTEL_EXPORTER_OTLP_ENDPOINT                              | The OTLP traces, metrics, and logs endpoint to connect to. Must be a URL with a scheme of either `http` or `https` based on the use of TLS. If protocol is `http/protobuf` the version and signal will be appended to the path (e.g. `v1/traces`, `v1/metrics`, or `v1/logs`). Default is `http://localhost:4317` when protocol is `grpc`, and `http://localhost:4318/v1/{signal}` when protocol is `http/protobuf`. |
| otel.exporter.otlp.traces.endpoint                       | OTEL_EXPORTER_OTLP_TRACES_ENDPOINT                       | The OTLP traces endpoint to connect to. Must be a URL with a scheme of either `http` or `https` based on the use of TLS. Default is `http://localhost:4317` when protocol is `grpc`, and `http://localhost:4318/v1/traces` when protocol is `http/protobuf`.                                                                                                                                                         |
| otel.exporter.otlp.metrics.endpoint                      | OTEL_EXPORTER_OTLP_METRICS_ENDPOINT                      | The OTLP metrics endpoint to connect to. Must be a URL with a scheme of either `http` or `https` based on the use of TLS. Default is `http://localhost:4317` when protocol is `grpc`, and `http://localhost:4318/v1/metrics` when protocol is `http/protobuf`.                                                                                                                                                       |
| otel.exporter.otlp.logs.endpoint                         | OTEL_EXPORTER_OTLP_LOGS_ENDPOINT                         | The OTLP logs endpoint to connect to. Must be a URL with a scheme of either `http` or `https` based on the use of TLS. Default is `http://localhost:4317` when protocol is `grpc`, and `http://localhost:4318/v1/logs` when protocol is `http/protobuf`.                                                                                                                                                             |
| otel.exporter.otlp.certificate                           | OTEL_EXPORTER_OTLP_CERTIFICATE                           | The path to the file containing trusted certificates to use when verifying an OTLP trace, metric, or log server's TLS credentials. The file should contain one or more X.509 certificates in PEM format. By default the host platform's trusted root certificates are used.                                                                                                                                          |
| otel.exporter.otlp.traces.certificate                    | OTEL_EXPORTER_OTLP_TRACES_CERTIFICATE                    | The path to the file containing trusted certificates to use when verifying an OTLP trace server's TLS credentials. The file should contain one or more X.509 certificates in PEM format. By default the host platform's trusted root certificates are used.                                                                                                                                                          |
| otel.exporter.otlp.metrics.certificate                   | OTEL_EXPORTER_OTLP_METRICS_CERTIFICATE                   | The path to the file containing trusted certificates to use when verifying an OTLP metric server's TLS credentials. The file should contain one or more X.509 certificates in PEM format. By default the host platform's trusted root certificates are used.                                                                                                                                                         |
| otel.exporter.otlp.logs.certificate                      | OTEL_EXPORTER_OTLP_LOGS_CERTIFICATE                      | The path to the file containing trusted certificates to use when verifying an OTLP log server's TLS credentials. The file should contain one or more X.509 certificates in PEM format. By default the host platform's trusted root certificates are used.                                                                                                                                                            |
| otel.exporter.otlp.client.key                            | OTEL_EXPORTER_OTLP_CLIENT_KEY                            | The path to the file containing private client key to use when verifying an OTLP trace, metric, or log client's TLS credentials. The file should contain one private key PKCS8 PEM format. By default no client key is used.                                                                                                                                                                                         |
| otel.exporter.otlp.traces.client.key                     | OTEL_EXPORTER_OTLP_TRACES_CLIENT_KEY                     | The path to the file containing private client key to use when verifying an OTLP trace client's TLS credentials. The file should contain one private key PKCS8 PEM format. By default no client key file is used.                                                                                                                                                                                                    |
| otel.exporter.otlp.metrics.client.key                    | OTEL_EXPORTER_OTLP_METRICS_CLIENT_KEY                    | The path to the file containing private client key to use when verifying an OTLP metric client's TLS credentials. The file should contain one private key PKCS8 PEM format. By default no client key file is used.                                                                                                                                                                                                   |
| otel.exporter.otlp.logs.client.key                       | OTEL_EXPORTER_OTLP_LOGS_CLIENT_KEY                       | The path to the file containing private client key to use when verifying an OTLP log client's TLS credentials. The file should contain one private key PKCS8 PEM format. By default no client key file is used.                                                                                                                                                                                                      |
| otel.exporter.otlp.client.certificate                    | OTEL_EXPORTER_OTLP_CLIENT_CERTIFICATE                    | The path to the file containing trusted certificates to use when verifying an OTLP trace, metric, or log client's TLS credentials. The file should contain one or more X.509 certificates in PEM format. By default no chain file is used.                                                                                                                                                                           |
| otel.exporter.otlp.traces.client.certificate             | OTEL_EXPORTER_OTLP_TRACES_CLIENT_CERTIFICATE             | The path to the file containing trusted certificates to use when verifying an OTLP trace server's TLS credentials. The file should contain one or more X.509 certificates in PEM format. By default no chain file is used.                                                                                                                                                                                           |
| otel.exporter.otlp.metrics.client.certificate            | OTEL_EXPORTER_OTLP_METRICS_CLIENT_CERTIFICATE            | The path to the file containing trusted certificates to use when verifying an OTLP metric server's TLS credentials. The file should contain one or more X.509 certificates in PEM format. By default no chain file is used.                                                                                                                                                                                          |
| otel.exporter.otlp.logs.client.certificate               | OTEL_EXPORTER_OTLP_LOGS_CLIENT_CERTIFICATE               | The path to the file containing trusted certificates to use when verifying an OTLP log server's TLS credentials. The file should contain one or more X.509 certificates in PEM format. By default no chain file is used.                                                                                                                                                                                             |
| otel.exporter.otlp.headers                               | OTEL_EXPORTER_OTLP_HEADERS                               | Key-value pairs separated by commas to pass as request headers on OTLP trace, metric, and log requests.                                                                                                                                                                                                                                                                                                              |
| otel.exporter.otlp.traces.headers                        | OTEL_EXPORTER_OTLP_TRACES_HEADERS                        | Key-value pairs separated by commas to pass as request headers on OTLP trace requests.                                                                                                                                                                                                                                                                                                                               |
| otel.exporter.otlp.metrics.headers                       | OTEL_EXPORTER_OTLP_METRICS_HEADERS                       | Key-value pairs separated by commas to pass as request headers on OTLP metrics requests.                                                                                                                                                                                                                                                                                                                             |
| otel.exporter.otlp.logs.headers                          | OTEL_EXPORTER_OTLP_LOGS_HEADERS                          | Key-value pairs separated by commas to pass as request headers on OTLP logs requests.                                                                                                                                                                                                                                                                                                                                |
| otel.exporter.otlp.compression                           | OTEL_EXPORTER_OTLP_COMPRESSION                           | The compression type to use on OTLP trace, metric, and log requests. Options include `gzip`. By default no compression will be used.                                                                                                                                                                                                                                                                                 |
| otel.exporter.otlp.traces.compression                    | OTEL_EXPORTER_OTLP_TRACES_COMPRESSION                    | The compression type to use on OTLP trace requests. Options include `gzip`. By default no compression will be used.                                                                                                                                                                                                                                                                                                  |
| otel.exporter.otlp.metrics.compression                   | OTEL_EXPORTER_OTLP_METRICS_COMPRESSION                   | The compression type to use on OTLP metric requests. Options include `gzip`. By default no compression will be used.                                                                                                                                                                                                                                                                                                 |
| otel.exporter.otlp.logs.compression                      | OTEL_EXPORTER_OTLP_LOGS_COMPRESSION                      | The compression type to use on OTLP log requests. Options include `gzip`. By default no compression will be used.                                                                                                                                                                                                                                                                                                    |
| otel.exporter.otlp.timeout                               | OTEL_EXPORTER_OTLP_TIMEOUT                               | The maximum waiting time, in milliseconds, allowed to send each OTLP trace, metric, and log batch. Default is `10000`.                                                                                                                                                                                                                                                                                               |
| otel.exporter.otlp.traces.timeout                        | OTEL_EXPORTER_OTLP_TRACES_TIMEOUT                        | The maximum waiting time, in milliseconds, allowed to send each OTLP trace batch. Default is `10000`.                                                                                                                                                                                                                                                                                                                |
| otel.exporter.otlp.metrics.timeout                       | OTEL_EXPORTER_OTLP_METRICS_TIMEOUT                       | The maximum waiting time, in milliseconds, allowed to send each OTLP metric batch. Default is `10000`.                                                                                                                                                                                                                                                                                                               |
| otel.exporter.otlp.logs.timeout                          | OTEL_EXPORTER_OTLP_LOGS_TIMEOUT                          | The maximum waiting time, in milliseconds, allowed to send each OTLP log batch. Default is `10000`.                                                                                                                                                                                                                                                                                                                  |
| otel.exporter.otlp.protocol                              | OTEL_EXPORTER_OTLP_PROTOCOL                              | The transport protocol to use on OTLP trace, metric, and log requests. Options include `grpc` and `http/protobuf`. Default is `grpc`.                                                                                                                                                                                                                                                                                |
| otel.exporter.otlp.traces.protocol                       | OTEL_EXPORTER_OTLP_TRACES_PROTOCOL                       | The transport protocol to use on OTLP trace requests. Options include `grpc` and `http/protobuf`. Default is `grpc`.                                                                                                                                                                                                                                                                                                 |
| otel.exporter.otlp.metrics.protocol                      | OTEL_EXPORTER_OTLP_METRICS_PROTOCOL                      | The transport protocol to use on OTLP metric requests. Options include `grpc` and `http/protobuf`. Default is `grpc`.                                                                                                                                                                                                                                                                                                |
| otel.exporter.otlp.logs.protocol                         | OTEL_EXPORTER_OTLP_LOGS_PROTOCOL                         | The transport protocol to use on OTLP log requests. Options include `grpc` and `http/protobuf`. Default is `grpc`.                                                                                                                                                                                                                                                                                                   |
| otel.exporter.otlp.metrics.temporality.preference        | OTEL_EXPORTER_OTLP_METRICS_TEMPORALITY_PREFERENCE        | The preferred output aggregation temporality. Options include `DELTA` and `CUMULATIVE`. If `CUMULATIVE`, all instruments will have cumulative temporality. If `DELTA`, counter (sync and async) and histograms will be delta, up down counters (sync and async) will be cumulative. Default is `CUMULATIVE`.                                                                                                         |
| otel.exporter.otlp.metrics.default.histogram.aggregation | OTEL_EXPORTER_OTLP_METRICS_DEFAULT_HISTOGRAM_AGGREGATION | The preferred default histogram aggregation. Options include `BASE2_EXPONENTIAL_BUCKET_HISTOGRAM` and `EXPLICIT_BUCKET_HISTOGRAM`. Default is `EXPLICIT_BUCKET_HISTOGRAM`.                                                                                                                                                                                                                                           |
| otel.experimental.exporter.otlp.retry.enabled            | OTEL_EXPERIMENTAL_EXPORTER_OTLP_RETRY_ENABLED            | If `true`, enable [experimental retry support](#otlp-exporter-retry). Default is `false`.                                                                                                                                                                                                                                                                                                                            |

#### OTLP exporter retry

[OTLP](https://github.com/open-telemetry/opentelemetry-specification/blob/main/specification/protocol/otlp.md#otlpgrpc-response) requires that [transient](https://github.com/open-telemetry/opentelemetry-specification/blob/main/specification/protocol/exporter.md#retry) errors be handled with a retry strategy. When retry is enabled, retryable gRPC status codes will be retried using an exponential backoff with jitter algorithm as described in the [gRPC Retry Design](https://github.com/grpc/proposal/blob/master/A6-client-retries.md#exponential-backoff).

The policy has the following configuration, which there is currently no way to customize.

- `maxAttempts`: The maximum number of attempts, including the original request. Defaults to `5`.
- `initialBackoff`: The initial backoff duration. Defaults to `1s`
- `maxBackoff`: The maximum backoff duration. Defaults to `5s`.
- `backoffMultiplier` THe backoff multiplier. Defaults to `1.5`.

## Manual Instrumentation

Reference Link: <https://opentelemetry.io/docs/instrumentation/java/manual/>

### Setup

Add dependencies required in maven on gradle based on what is being used for the project

#### Maven

```
<project>
    <dependencyManagement>
        <dependencies>
            <dependency>
                <groupId>io.opentelemetry</groupId>
                <artifactId>opentelemetry-bom</artifactId>
                <version>1.27.0</version>
                <type>pom</type>
                <scope>import</scope>
            </dependency>
        </dependencies>
    </dependencyManagement>

    <dependencies>
        <dependency>
            <groupId>io.opentelemetry</groupId>
            <artifactId>opentelemetry-api</artifactId>
        </dependency>
        <dependency>
            <groupId>io.opentelemetry</groupId>
            <artifactId>opentelemetry-sdk</artifactId>
        </dependency>
        <dependency>
            <groupId>io.opentelemetry</groupId>
            <artifactId>opentelemetry-exporter-otlp</artifactId>
        </dependency>
        <dependency>
            <groupId>io.opentelemetry</groupId>
            <artifactId>opentelemetry-semconv</artifactId>
            <version>1.27.0-alpha</version>
        </dependency>
    </dependencies>
</project>
```

#### Gradle

```
dependencies {
    implementation 'io.opentelemetry:opentelemetry-api:1.27.0'
    implementation 'io.opentelemetry:opentelemetry-sdk:1.27.0'
    implementation 'io.opentelemetry:opentelemetry-exporter-otlp:1.27.0'
    implementation 'io.opentelemetry:opentelemetry-semconv:1.27.0-alpha'
}
```

#### Imports to Use

```java
import io.opentelemetry.api.OpenTelemetry;
import io.opentelemetry.api.common.Attributes;
import io.opentelemetry.api.trace.propagation.W3CTraceContextPropagator;
import io.opentelemetry.context.propagation.ContextPropagators;
import io.opentelemetry.exporter.otlp.trace.OtlpGrpcSpanExporter;
import io.opentelemetry.sdk.OpenTelemetrySdk;
import io.opentelemetry.sdk.resources.Resource;
import io.opentelemetry.sdk.trace.SdkTracerProvider;
import io.opentelemetry.sdk.trace.export.BatchSpanProcessor;
import io.opentelemetry.semconv.resource.attributes.ResourceAttributes;

```

#### Instrumentation example

```java
    Resource resource = Resource.getDefault()
            .merge(Resource.create(Attributes.of(ResourceAttributes.SERVICE_NAME, "java-manual-instrumentation")));

    SdkTracerProvider sdkTracerProvider = SdkTracerProvider.builder().addSpanProcessor(
            BatchSpanProcessor.builder(OtlpGrpcSpanExporter.builder()
                    .setEndpoint("http://0.0.0.0:9090")
                    .setTimeout(Duration.ofSeconds(10)).build()).build()).setResource(resource).build();

    OpenTelemetry openTelemetry = OpenTelemetrySdk.builder().setTracerProvider(sdkTracerProvider)
            .setPropagators(ContextPropagators.create(W3CTraceContextPropagator.getInstance()))
            .buildAndRegisterGlobal();

    @RequestMapping(method = RequestMethod.GET, path = "/ping")
    public ResponseEntity<String> getPing() {


        Tracer tracer = openTelemetry.getTracer("java-manual-example", "1.0.0");
        Span span = tracer.spanBuilder("/ping").startSpan();
        span.setAttribute("http.method", "GET");
        span.setAttribute("instrumentation.type", "manual");


        try (Scope ss = span.makeCurrent()) {
            return ResponseEntity.ok("pong");
        } finally {
            span.end();
        }

    }
```

check out the example in the attachment for simple manual instrumentation
