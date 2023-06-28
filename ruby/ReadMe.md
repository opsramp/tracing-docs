# Ruby

## Instrumentation

### Installing Dependencies

```bash
gem install --user-install opentelemetry-sdk opentelemetry-instrumentation-all opentelemetry-exporter-otlp
```

### Defining Tracer

```ruby
# If in a rails app, this lives in config/initializers/opentelemetry.rb
require "opentelemetry/sdk"

ENV['OTEL_TRACES_EXPORTER'] ||= 'otlp'
ENV['OTEL_EXPORTER_OTLP_PROTOCOL'] ||= 'http/protobuf'
ENV['OTEL_EXPORTER_OTLP_ENDPOINT'] ||= 'http://0.0.0.0:8082'

OpenTelemetry::SDK.configure do |c|
  c.service_name = '<YOUR_SERVICE_NAME>'
  c.use_all() # enables all instrumentation!
end

# 'Tracer' can be used throughout your code now
MyAppTracer = OpenTelemetry.tracer_provider.tracer('<YOUR_TRACER_NAME>')

```

### Creating New Spans

#### Single Span

```ruby
require "opentelemetry/sdk"

def do_work
  MyAppTracer.in_span("do_work") do |span|

    # To add attributes in spans
    span.add_attributes({
        "my.cool.attribute" => "a value",
        "my.first.name" => "Oscar"
    })

    # To add events to span
    span.add_event("too many bugs")

    # To set span as error
    span.status = OpenTelemetry::Trace::Status.error("error message here!")

    # do some work that the 'do_work' span tracks!
  end
end
```

#### Nested Span

```ruby
require "opentelemetry/sdk"

def parent_work
  MyAppTracer.in_span("parent") do |span|
    # do some work that the 'parent' span tracks!

    child_work

    # do some more work afterwards
  end
end

def child_work
  MyAppTracer.in_span("child") do |span|
    # do some work that the 'child' span tracks!
  end
end
```

To find the code in the example look at the following files

* dice-ruby/config/initializers/opentelemetry.rb
* dice-ruby/app/controllers/dice_controller.rb
