# Python

Reference Link: <https://opentelemetry.io/docs/instrumentation/python/automatic/>

OpenTelemetry python auto instrumentation has instrumentation for all the following libraries present in this link: <https://opentelemetry.io/ecosystem/registry/?language=python&component=instrumentation>

## Auto Instrumentation

### Installing Dependencies

Run the following commands in python virtual environment or globally depending on how the project dependencies are handled

```bash
# Installing the dependencies
$ pip install opentelemetry-distro \
 opentelemetry-exporter-otlp

# Scanning through the list of packages installed in your active site-packages folder, 
# and installs the corresponding instrumentation libraries for these packages, if applicable.
$ opentelemetry-bootstrap -a install

```

### Configuring the agent

```bash
$ opentelemetry-instrument \
    --traces_exporter otlp \
    --service_name <your-service-name> \
    --metrics_exporter none \
    --exporter_otlp_endpoint http://localhost:9090 \
    python <application_name>.py

```

or we can also use Environment variables to configure the same properties

```bash
$ OTEL_SERVICE_NAME=<your-service-name> \
  OTEL_TRACES_EXPORTER=otlp \
  OTEL_METRICS_EXPORTER=none \
  OTEL_EXPORTER_OTLP_TRACES_ENDPOINT=http://0.0.0.0:9090
  opentelemetry-instrument \
      python <application_name>.py

```

> **Note**
> All the agent configuration can be found at: <https://opentelemetry.io/docs/instrumentation/python/automatic/agent-config/>

## Manual Instrumentation

Reference links: <https://opentelemetry.io/docs/instrumentation/python/manual/>

check the example in manual.py for a simple trace code example
