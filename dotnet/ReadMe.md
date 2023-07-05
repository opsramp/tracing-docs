# Dotnet

Reference Link: https://opentelemetry.io/docs/instrumentation/net/getting-started/

## Prerequisites

OpenTelemetry for .NET supports all officially supported versions of .NET Core and .NET Framework except for .NET Framework 3.5 SP1.

- Installing dependencies

    ```bash
  dotnet add package OpenTelemetry.Extensions.Hosting
  dotnet add package OpenTelemetry.Instrumentation.AspNetCore --prerelease
      ```


    Go through the Automatic and Manual folders for finding the instrumentation of Hello world web app.

    # Note:
    - Set environment variables as required to push traces from an web app
    ```bash
    OTEL_EXPORTER_OTLP_ENDPOINT=http://localhost:4317
    export OTEL_EXPORTER_OTLP_ENDPOINT
      ```
