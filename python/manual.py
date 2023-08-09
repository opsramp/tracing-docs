import json
from flask import Flask
from datetime import datetime
from opentelemetry import trace
from opentelemetry.sdk.trace import TracerProvider
from opentelemetry.sdk.trace.export import (
    BatchSpanProcessor,
)

from opentelemetry.exporter.otlp.proto.grpc.trace_exporter import OTLPSpanExporter
from opentelemetry.sdk.resources import SERVICE_NAME, Resource
from opentelemetry.sdk.trace import TracerProvider
from opentelemetry.trace import Status, StatusCode


## Defining the resource attributes #####
resource = Resource(attributes={
    SERVICE_NAME: "flask_manual_example"
})
#########################################


## Creating a Tracer and Expoter ########
provider = TracerProvider(resource=resource)
processor = BatchSpanProcessor(
    OTLPSpanExporter(endpoint="http://0.0.0.0:9090"))
provider.add_span_processor(processor)


#########################################

# Sets the global default tracer provider
trace.set_tracer_provider(provider)

# Creates a tracer from the global tracer provider
tracer = trace.get_tracer("library_flask_test")


app = Flask(__name__)


@app.route('/time')
def index():
    with tracer.start_as_current_span("time") as span:
        now = datetime.now()
        current_time = now.strftime("%H:%M:%S")

        # settings attributes
        span.set_attribute("http.request", "/time")

        span.add_event("a simple api which display the current time")

        span.set_status(Status(StatusCode.ERROR))

        with tracer.start_as_current_span("child_of_time") as child:

            print("doing something")

        return json.dumps(current_time)


app.run()


