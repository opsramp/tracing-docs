package com.example.otelexample;

import io.opentelemetry.api.trace.Tracer;
import io.opentelemetry.context.Scope;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RequestMethod;
import org.springframework.web.bind.annotation.RestController;
import io.opentelemetry.api.OpenTelemetry;
import io.opentelemetry.api.trace.Span;

import io.opentelemetry.api.common.Attributes;
import io.opentelemetry.api.trace.propagation.W3CTraceContextPropagator;
import io.opentelemetry.context.propagation.ContextPropagators;

import io.opentelemetry.exporter.otlp.trace.OtlpGrpcSpanExporter;

import io.opentelemetry.sdk.OpenTelemetrySdk;

import io.opentelemetry.sdk.resources.Resource;
import io.opentelemetry.sdk.trace.SdkTracerProvider;
import io.opentelemetry.sdk.trace.export.BatchSpanProcessor;

import io.opentelemetry.semconv.resource.attributes.ResourceAttributes;

import java.time.Duration;

@RestController
public class PingRestController {
    Resource resource = Resource.getDefault()
            .merge(Resource.create(Attributes.of(ResourceAttributes.SERVICE_NAME, "java-manual-instrumentation")));

    SdkTracerProvider sdkTracerProvider = SdkTracerProvider.builder().addSpanProcessor(
            BatchSpanProcessor.builder(OtlpGrpcSpanExporter.builder()
                    .setEndpoint("http://0.0.0.0:9090")
                    .setTimeout(Duration.ofSeconds(10)).build()).build()).setResource(resource).build();

    OpenTelemetry openTelemetry = OpenTelemetrySdk.builder().setTracerProvider(sdkTracerProvider)
            .setPropagators(ContextPropagators.create(W3CTraceContextPropagator.getInstance()))
            .buildAndRegisterGlobal();

    @RequestMapping(method = RequestMethod.GET, path = "/ping")
    public ResponseEntity<String> getPing() {


        Tracer tracer = openTelemetry.getTracer("java-manual-example", "1.0.0");
        Span span = tracer.spanBuilder("/ping").startSpan();
        span.setAttribute("http.method", "GET");
        span.setAttribute("instrumentation.type", "manual");


        try (Scope ss = span.makeCurrent()) {
            return ResponseEntity.ok("pong");
        } finally {
            span.end();
        }

    }

    @RequestMapping(method = RequestMethod.GET, path = "/random")
    public ResponseEntity<Double> getRollDice() {

        Tracer tracer = openTelemetry.getTracer("java-manual-example", "1.0.0");
        Span span = tracer.spanBuilder("/random").startSpan();
        span.setAttribute("http.method", "GET");
        span.setAttribute("instrumentation.type", "manual");

        try (Scope ss = span.makeCurrent()) {
            return ResponseEntity.ok(Math.random());
        } finally {
            span.end();
        }


    }

}
