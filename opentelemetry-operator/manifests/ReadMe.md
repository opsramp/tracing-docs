# Deploying MicroServices Demo application with OTEL Operator

This is a sample application deployment with OTEL Operator with only the auto instrumentation configured (The collector is not configured here in this example)

## Prerequisites

- must have cert manager installed [cert-manager](https://cert-manager.io/docs/installation/)

## Deploying OTEL Operator

```bash
kubectl apply -f manifests/opentelemetry-operator.yml
```

## Deploying MicroServices Demo application

```bash
# Deploying the microservices demo application with required annotations for instrumentation sidecar
kubectl apply -f manifests/microservices-demo-non-instrumented.yml
```

## Deploying the Instrumentation custom kubernetes resource

Change the endpoints in this file to endpoints where OTEL Receivers are configured

```bash
kubectl apply -f manifests/instrumentation.yml
```
