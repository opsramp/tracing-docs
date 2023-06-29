# GoLang

Reference Link: <https://opentelemetry.io/docs/instrumentation/go/getting-started/>

## Prerequisites

- Go Version must be higher than 1.16

- Installing dependencies

    ```bash
    go get go.opentelemetry.io/otel \
        go.opentelemetry.io/otel/trace \
        go.opentelemetry.io/otel/sdk \
        go.opentelemetry.io/otel/exporters/otlp/otlptrace

    ```

## Instrumentation

Follow the steps at: <https://opentelemetry.io/docs/instrumentation/go/>

For example of spans check the handlers directory and for details on how to create a tracer check tracing directory
