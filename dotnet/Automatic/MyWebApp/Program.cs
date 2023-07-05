using System.Diagnostics;
using OpenTelemetry.Resources;
using OpenTelemetry.Trace;



var builder = WebApplication.CreateBuilder(args);




builder.Services.AddOpenTelemetry()
    .WithTracing(tracerBuilder => tracerBuilder
        .AddSource(DiagnosticsConfig.ActivitySource.Name)
        .ConfigureResource(resource => resource
        .AddService(DiagnosticsConfig.ServiceName))
        .AddAspNetCoreInstrumentation()
        .AddOtlpExporter()
        .AddConsoleExporter());
 


var app = builder.Build();



app.MapGet("/", () => "Hello World!");

app.Run();


public static class DiagnosticsConfig
{
    public const string ServiceName = "MyService";
    public static ActivitySource ActivitySource = new ActivitySource(ServiceName);
}

