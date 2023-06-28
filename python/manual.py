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



resource = Resource(attributes={
    SERVICE_NAME: "flask_manual_example"
})

provider = TracerProvider(resource=resource)
processor = BatchSpanProcessor(OTLPSpanExporter(endpoint="http://0.0.0.0:9090"))
provider.add_span_processor(processor)

# Sets the global default tracer provider
trace.set_tracer_provider(provider)

# Creates a tracer from the global tracer provider
tracer = trace.get_tracer("manual_flask")


app = Flask(__name__)


@app.route('/time')
def index():
    with tracer.start_as_current_span("time") as span:
        now = datetime.now()
        current_time = now.strftime("%H:%M:%S")
        return json.dumps(current_time)

app.run()



