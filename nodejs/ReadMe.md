# NodeJS

## Auto Instrumentation

Reference Link: <https://opentelemetry.io/docs/instrumentation/js/automatic/>

opentelemetry js instrumentation has support for all the js libraries specified here: <https://github.com/open-telemetry/opentelemetry-js-contrib/tree/main/metapackages/auto-instrumentations-node#supported-instrumentations>

### Setup

```bash
npm install --save @opentelemetry/api
npm install --save @opentelemetry/auto-instrumentations-node
```

### Configuring the agent

```bash
$ OTEL_TRACES_EXPORTER="otlp" \
    OTEL_METRICS_EXPORTER="none" \
    OTEL_EXPORTER_OTLP_PROTOCOL="http/protobuf" \ 
    OTEL_EXPORTER_OTLP_ENDPOINT="http://0.0.0.0:8082" \
    OTEL_NODE_RESOURCE_DETECTORS="env,host,os" \
    OTEL_SERVICE_NAME="<servie_name>" \
    OTEL_EXPORTER_OTLP_COMPRESSION="gzip" \
    NODE_OPTIONS="--require @opentelemetry/auto-instrumentations-node/register" \
    node <application_name>.js
```

By default, all SDK resource detectors are used. You can use the environment variable OTEL_NODE_RESOURCE_DETECTORS to enable only certain detectors, or completely disable them.

To see the full range of configuration options, see [Module Configuration](https://opentelemetry.io/docs/instrumentation/js/automatic/module-config/).

## Manual Instrumentation

Reference link: <https://opentelemetry.io/docs/instrumentation/js/manual/>

### Setup

To start tracing, you'll need to have an initialized TracerProvider that will let you create a Tracer.

If a TracerProvider is not created, the OpenTelemetry APIs for tracing will use a no-op implementation and fail to generate data.

Node.js
To initialize tracing with the Node.js SDK, first ensure you have the SDK package and OpenTelemetry API installed:

```bash
npm install \
  @opentelemetry/api \
  @opentelemetry/resources \
  @opentelemetry/semantic-conventions \
  @opentelemetry/sdk-trace-node \
  @opentelemetry/instrumentation
```

Next use this following code in one of the files of your project for SDK initialization

```javascript
const { Resource } = require("@opentelemetry/resources");
const { SemanticResourceAttributes } = require("@opentelemetry/semantic-conventions");
const { NodeTracerProvider } = require("@opentelemetry/sdk-trace-node");
const { registerInstrumentations } = require("@opentelemetry/instrumentation");
const { ConsoleSpanExporter, BatchSpanProcessor } = require("@opentelemetry/sdk-trace-base");

// Optionally register instrumentation libraries
registerInstrumentations({
  instrumentations: [],
});

const resource =
  Resource.default().merge(
    new Resource({
      [SemanticResourceAttributes.SERVICE_NAME]: "service-name-here",
      [SemanticResourceAttributes.SERVICE_VERSION]: "0.1.0",
    })
  );

const provider = new NodeTracerProvider({
    resource: resource,
});
const exporter = new ConsoleSpanExporter();
const processor = new BatchSpanProcessor(exporter);
provider.addSpanProcessor(processor);

provider.register();
```

### Run

```bash
node --require ./tracing.js <app-file.js>
```
