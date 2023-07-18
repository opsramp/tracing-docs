# C++ OpenTelemetry Traces Instrumentation

## Prerequisites

```bash
sudo apt-get install git cmake g++ libcurl4-openssl-dev 

# Installing headers for protobuf-cpp
sudo apt install libprotobuf-dev libprotoc-dev protobuf-compiler

# Installing headers for GRPC cpp
https://github.com/grpc/grpc/blob/master/BUILDING.md
```

## Building OpenTelemetry-CPP

opentelemetry-cpp requires GRPC so that needs to be compiled first: <https://github.com/grpc/grpc/blob/master/BUILDING.md>

```bash
# Clone the repository
git clone --recursive https://github.com/open-telemetry/opentelemetry-cpp

# Create CMake build configurations
cd opentelemetry-cpp
mkdir build && cd build

cmake -DBUILD_TESTING=OFF -DCMAKE_POSITION_INDEPENDENT_CODE=ON -DBUILD_SHARED_LIBS=ON -DWITH_OTLP_GRPC=ON -DWITH_OTLP_HTTP=ON ..

cmake --build . --target all

cmake --install . --prefix <path_to_your_config>
```

## Initializing Tracer

```cpp
auto provider = opentelemetry::trace::Provider::GetTracerProvider();
auto tracer = provider->GetTracer("foo_library", "1.0.0");
```

GetTracerProvider() gives us a provider from which we create a tracer which is a singleton object from which all the spans are created.

## Starting a Span

```cpp
auto span = tracer->StartSpan("HandleRequest");
```

This creates a span with operation name *HandleRequest*

### Marking the span active

```cpp
auto scope = tracer->WithActiveSpan(span);
```

This returns a Scope object and designates a span as active. The span's active period is determined by the scope object. For the duration of the scope object, the span is still in effect.

The idea of an active span is crucial because every span established without specifically naming a parent gets parented to the one that is presently active. Root span is a span that has no parents.

## Nesting Spans

```cpp
auto outer_span = tracer->StartSpan("Outer operation");
auto outer_scope = tracer->WithActiveSpan(outer_span);
{
    auto inner_span = tracer->StartSpan("Inner operation");
    auto inner_scope = tracer->WithActiveSpan(inner_span);
    // ... perform inner operation
    inner_span->End();
}
// ... perform outer operation
outer_span->End();
```

spans can be nested and each new span associates itself as a child with the span that is currently marked as active.

## Exporters

### HTTP Exporter

```cpp
#include "opentelemetry/exporters/otlp/otlp_http_exporter_factory.h"
#include "opentelemetry/exporters/otlp/otlp_http_exporter_options.h"

namespace otlp = opentelemetry::exporter::otlp;

otlp::OtlpHttpExporterOptions opts;
opts.url = "http://localhost:8082/v1/traces";

auto exporter = otlp::OtlpHttpExporterFactory::Create(opts);
```

### GRPC Exporter

```cpp
#include "opentelemetry/exporters/otlp/otlp_grpc_exporter_factory.h"
#include "opentelemetry/exporters/otlp/otlp_grpc_exporter_options.h"

namespace otlp = opentelemetry::exporter::otlp;

otlp::OtlpGrpcExporterOptions opts;
opts.endpoint = "localhost:9090";
opts.use_ssl_credentials = true;
opts.ssl_credentials_cacert_as_string = "ssl-certificate";

auto exporter = otlp::OtlpGrpcExporterFactory::Create(opts);
```

Additional Reference: <https://github.com/open-telemetry/opentelemetry-cpp/blob/main/examples/otlp/README.md>
