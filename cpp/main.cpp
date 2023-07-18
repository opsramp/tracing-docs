#include <iostream>
#include <unistd.h>
#include "opentelemetry/exporters/ostream/span_exporter_factory.h"
#include "opentelemetry/sdk/trace/exporter.h"
#include "opentelemetry/sdk/trace/processor.h"
#include "opentelemetry/sdk/trace/simple_processor_factory.h"
#include "opentelemetry/sdk/trace/tracer_provider_factory.h"
#include "opentelemetry/trace/provider.h"
#include "opentelemetry/exporters/otlp/otlp_http_exporter_factory.h"
#include "opentelemetry/exporters/otlp/otlp_http_exporter_options.h"

bool is_leap_year(int year)
{
    return ((year % 4 == 0) && (year % 100 != 0 || year % 400 == 0));
}

namespace trace_api = opentelemetry::trace;
namespace trace_sdk = opentelemetry::sdk::trace;
namespace trace_exporter = opentelemetry::exporter::trace;
namespace otlp = opentelemetry::exporter::otlp;

opentelemetry::v1::nostd::shared_ptr<opentelemetry::v1::trace::Tracer> tracer;

void InitTracer()
{
    otlp::OtlpHttpExporterOptions opts;
    opts.url = "http://localhost:8082/v1/traces";

    auto exporter = otlp::OtlpHttpExporterFactory::Create(opts);

    auto processor = trace_sdk::SimpleSpanProcessorFactory::Create(std::move(exporter));
    std::shared_ptr<opentelemetry::trace::TracerProvider> provider =
        trace_sdk::TracerProviderFactory::Create(std::move(processor));

    // Set the global trace provider
    trace_api::Provider::SetTracerProvider(provider);

    tracer = provider->GetTracer("cpp_otel_library", "1.0.0");
}

void CleanupTracer()
{
    std::shared_ptr<opentelemetry::trace::TracerProvider> none;
    trace_api::Provider::SetTracerProvider(none);
}

int main()
{
    // Removing this line will leave the default noop TracerProvider in place.
    InitTracer();

    for (int i = 2000; i < 2100; i++)
    {
        auto span = tracer->StartSpan("check leap");

        if (is_leap_year(i))
        {
            std::cout << i << " is leap" << std::endl;
        }
        else
        {
            std::cout << i << " is not leap" << std::endl;
        }

        span->End();

        sleep(5);
    }

    CleanupTracer();
}