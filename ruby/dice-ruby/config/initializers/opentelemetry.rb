# If in a rails app, this lives in config/initializers/opentelemetry.rb
require "opentelemetry/sdk"

ENV['OTEL_TRACES_EXPORTER'] ||= 'otlp'
ENV['OTEL_EXPORTER_OTLP_PROTOCOL'] ||= 'http/protobuf'
ENV['OTEL_EXPORTER_OTLP_ENDPOINT'] ||= 'http://0.0.0.0:8082'

OpenTelemetry::SDK.configure do |c|
  c.service_name = 'ruby-otel-instrumentation'
  c.use_all()
end

# 'Tracer' can be used throughout your code now
MyAppTracer = OpenTelemetry.tracer_provider.tracer('ruby-on-rails')
