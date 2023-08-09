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

### Acquiring a new tracer

```python
from opentelemetry import trace
from opentelemetry.sdk.trace import TracerProvider
from opentelemetry.sdk.trace.export import (
    BatchSpanProcessor,
)

## Creating a Tracer and Expoter ########
provider = TracerProvider(resource=resource)
processor = BatchSpanProcessor(
    OTLPSpanExporter(endpoint="http://0.0.0.0:9090"))
provider.add_span_processor(processor)

# Sets the global default tracer provider
trace.set_tracer_provider(provider)

# Creates a tracer from the global tracer provider with library.name
tracer = trace.get_tracer("library_name_here")
```

### Spans

```python
def do_work():
    with tracer.start_as_current_span("span-name") as span:
        # do some work that 'span' will track
        print("doing some work...")
        # When the 'with' block goes out of scope, 'span' is closed for you

```

#### creating child spans

```python
def do_work():
    with tracer.start_as_current_span("parent") as parent:
        # do some work that 'parent' tracks
        print("doing some work...")
        # Create a nested span to track nested work
        with tracer.start_as_current_span("child") as child:
            # do some work that 'child' tracks
            print("doing some nested work...")
            # the nested span is closed when it's out of scope

        # This span is also closed when it goes out of scope

```

#### adding attributes

```python
from opentelemetry import trace

with tracer.start_as_current_span("span-name") as span:
    span.set_attribute("operation.value", 1)
    span.set_attribute("operation.name", "Saying hello!")
    span.set_attribute("operation.other-stuff", [1, 2, 3])
```

#### adding events

```python
from opentelemetry import trace

with tracer.start_as_current_span("span-name") as span:
    span.add_event("something went wrong!")

```

#### setting status

```python

from opentelemetry import trace
from opentelemetry.trace import Status, StatusCode

with tracer.start_as_current_span("span-name") as span:
    span.set_status(Status(StatusCode.ERROR))
```
