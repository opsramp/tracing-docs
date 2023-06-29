package tracing

import (
	"context"

	"go.opentelemetry.io/otel/trace"
	"google.golang.org/grpc"
	"google.golang.org/grpc/credentials/insecure"
	"log"
	"os"
	"time"

	"go.opentelemetry.io/otel"
	"go.opentelemetry.io/otel/attribute"
	"go.opentelemetry.io/otel/exporters/otlp/otlptrace/otlptracegrpc"
	"go.opentelemetry.io/otel/sdk/resource"
	sdktrace "go.opentelemetry.io/otel/sdk/trace"
	semconv "go.opentelemetry.io/otel/semconv/v1.17.0"
)

// name is the Tracer name used to identify this instrumentation library.
const TracerName = "gin_gogonic"

var Tracer trace.Tracer
var tracerProvider *sdktrace.TracerProvider

func init() {
	ctx := context.Background()

	hostname, err := os.Hostname()
	if err != nil {
		log.Fatal(err)
	}

	// ============= Defining the resource attributes ========================
	res, err := resource.New(ctx,
		resource.WithAttributes(
			semconv.ServiceName("go-gonic"),
			attribute.KeyValue{
				Key:   "host",
				Value: attribute.StringValue(hostname),
			},
		),
	)
	if err != nil {
		log.Fatalf("failed to create resource: %v", err)
	}
	// =======================================================================
	//
	//
	//
	//
	//
	// ===================== Creating a Exporter =============================

	ctx, cancel := context.WithTimeout(ctx, time.Second)
	defer cancel()
	conn, err := grpc.DialContext(ctx, "0.0.0.0:9090",
		grpc.WithTransportCredentials(insecure.NewCredentials()),
		grpc.WithBlock(),
	)
	if err != nil {
		log.Fatalf("failed to create gRPC connection to collector: %v", err)
	}

	// Set up a trace exporter
	traceExporter, err := otlptracegrpc.New(ctx, otlptracegrpc.WithGRPCConn(conn))
	if err != nil {
		log.Fatalf("failed to create trace exporter: %v", err)
	}
	// =======================================================================
	//
	//
	//
	//
	//
	// ====================== Updating everything to global tracer ===========

	// Register the trace exporter with a TracerProvider, using a batch
	// span processor to aggregate spans before export.
	bsp := sdktrace.NewBatchSpanProcessor(traceExporter)
	tracerProvider = sdktrace.NewTracerProvider(
		sdktrace.WithResource(res),
		sdktrace.WithSpanProcessor(bsp),
	)
	otel.SetTracerProvider(tracerProvider)
	// =======================================================================

	// ===== create a global tracer
	Tracer = otel.Tracer(TracerName)
}
