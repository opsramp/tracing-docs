# Sample Installation of OTEL Operator for MicroServices Demo Application

## Deploying OTEL Operator

> **_Deployment Using Helm:_** <https://opentelemetry.io/docs/kubernetes/helm/operator/>
> 
> **_Deployment Using YAML:_** <https://opentelemetry.io/docs/kubernetes/operator/>

### Using Kubernetes YAML files

#### Prerequisites

- must have cert manager installed [cert-manager](https://cert-manager.io/docs/installation/)

#### Installing OTEL Operator

```bash
kubectl apply -f https://github.com/open-telemetry/opentelemetry-operator/releases/latest/download/opentelemetry-operator.yaml
```

### Using Helm Chart

```bash
helm repo add open-telemetry https://open-telemetry.github.io/opentelemetry-helm-charts
helm install my-opentelemetry-operator open-telemetry/opentelemetry-operator \
  --set admissionWebhooks.certManager.enabled=false \
  --set admissionWebhooks.certManager.autoGenerateCert=true
```

## Deploying the Instrumentation Custom Resource Definition

> **Reference Links**
>
> Instrumentation CRD: <https://github.com/open-telemetry/opentelemetry-operator/blob/main/docs/api.md#instrumentation>
>
> DotNet Auto Instrumentation: <https://opentelemetry.io/docs/instrumentation/net/automatic/>
>
> Java Auto Instrumentation: <https://opentelemetry.io/docs/instrumentation/java/automatic/agent-config/>
>
> Nodejs Auto Instrumentation: <https://opentelemetry.io/docs/instrumentation/js/libraries/#registration>
>
> Python Auto Instrumentation: <https://opentelemetry.io/docs/instrumentation/python/automatic/>
>
> Go EBPF Auto Instrumentation: <https://github.com/open-telemetry/opentelemetry-go-instrumentation>
>
> Propagators Supported: <https://opentelemetry.io/docs/specs/otel/context/api-propagators/#propagators-distribution>

To manage the instrumentation the operator needs to be configured with a Instrumentation CRD which specifying the configs for the auto instrumentation side cars for all the supported languages (like the example below)

you can find all the configs [here](https://github.com/open-telemetry/opentelemetry-operator/blob/main/docs/api.md#instrumentation)

```yaml
apiVersion: opentelemetry.io/v1alpha1
kind: Instrumentation
metadata:
  name: my-instrumentation
spec:
  exporter:
    endpoint: <scheme>://<ip_address>:<grpc_port>
  resource:
    addK8sUIDAttributes: true
  propagators:
    - tracecontext
    - baggage
  go:
    image: ghcr.io/open-telemetry/opentelemetry-go-instrumentation/autoinstrumentation-go:v0.3.0-alpha
  python:
    image: ghcr.io/open-telemetry/opentelemetry-operator/autoinstrumentation-python:0.40b0
    env:
      - name: OTEL_EXPORTER_OTLP_ENDPOINT
        value: <scheme>://<ip_address>:<grpc_port>
  dotnet:
    env:
      - name: OTEL_EXPORTER_OTLP_ENDPOINT
        value: <scheme>://<ip_address>:<grpc_port>

```

## To Check if Instrumentation resource is installed properly

```bash
kubectl describe otelinst -n <namespace>
```

## To Check Logs of OTEL Operator

```bash
kubectl logs -l app.kubernetes.io/name=opentelemetry-operator --container manager -n opentelemetry-operator-system --follow
```

## Controlling Instrumentation Capabilities

<https://github.com/open-telemetry/opentelemetry-operator#controlling-instrumentation-capabilities>

## Adding Annotations to Pods to attach the auto-instrumentation sidecar

> **_Reference Link:_** <https://opentelemetry.io/docs/kubernetes/operator/automatic/#add-annotations-to-existing-deployments>

To opt in services for auto-instrumentation we need to add annotations for its respective language so that the operator attach the sidecars

The annotation must be added to `spec.template.metadata.annotation`

- .NET      : `instrumentation.opentelemetry.io/inject-dotnet: "true"`
- Go        : `instrumentation.opentelemetry.io/inject-go: "true"`  
              `instrumentation.opentelemetry.io/otel-go-auto-target-exe: '/path/to/container/executable'`
- Java      : `instrumentation.opentelemetry.io/inject-java: "true"`
- Node.js   : `instrumentation.opentelemetry.io/inject-nodejs: "true"`
- Python    : `instrumentation.opentelemetry.io/inject-python: "true"`

If there are multiple pods the we can add the annotation with container names `instrumentation.opentelemetry.io/container-names: "container_name1,container_name2"`

The possible values for the annotation can be

- `"true"` - to inject Instrumentation resource with default name from the current namespace.
- `"my-instrumentation"` - to inject Instrumentation CR instance with name "my-instrumentation" in the current namespace.
- `"my-other-namespace/my-instrumentation"` - to inject Instrumentation CR instance with name "my-instrumentation" from another namespace "my-other-namespace".
- `"false"` - do not inject

We can also add the annotation directly to the namespace to have all the pods in the namespace to be auto-instrumented

## Deploying OpenTelemetry Collector

OpenTelemetry Operator allows deployment of OpenTelemetry Collector so that all the data is collected by these collectors first and then sent to the central storage location. Having collector in between instead of directly configuring the endpoint of the central server allows for features like retry on failure and batching

To Deploy OpenTelemetry Collector we use use a CRD called `OpenTelemetryCollector`

This `OpenTelemetryCollector` CRD allows us to deploy the collector in 4 ways

- Deployment
- DaemonSet
- StatefulSet
- Sidecar

### Sidecar

A sidecar with the OpenTelemetry Collector can be injected into pod-based workloads by setting the pod annotation `sidecar.opentelemetry.io/inject` to either `"true"`, or to the name of a concrete OpenTelemetryCollector, like in the following example:

```yaml
kubectl apply -f - <<EOF
apiVersion: opentelemetry.io/v1alpha1
kind: OpenTelemetryCollector
metadata:
  name: sidecar-for-my-app
spec:
  mode: sidecar
  config: |
    receivers:
      jaeger:
        protocols:
          thrift_compact:
    processors:

    exporters:
      logging:

    service:
      pipelines:
        traces:
          receivers: [jaeger]
          processors: []
          exporters: [logging]
EOF

kubectl apply -f - <<EOF
apiVersion: v1
kind: Pod
metadata:
  name: myapp
  annotations:
    sidecar.opentelemetry.io/inject: "true"
spec:
  containers:
  - name: myapp
    image: jaegertracing/vertx-create-span:operator-e2e-tests
    ports:
      - containerPort: 8080
        protocol: TCP
EOF
```

### Deployment

```yaml
---
apiVersion: opentelemetry.io/v1alpha1
kind: OpenTelemetryCollector
metadata:
  name: simplest
spec:
  mode: "deployment"
  ingress:
    type: ingress
    hostname: "example.com"
    annotations:
      something.com: "true"
  config: |
    receivers:
      otlp:
        protocols:
          grpc:
          http:

    exporters:
      logging:

    service:
      pipelines:
        traces:
          receivers: [otlp]
          processors: []
          exporters: [logging]
```

### DaemonSet

```yaml
apiVersion: opentelemetry.io/v1alpha1
kind: OpenTelemetryCollector
metadata:
  name: daemonset
spec:
  mode: daemonset
  hostNetwork: true
  config: |
    receivers:
      jaeger:
        protocols:
          grpc:
    processors:
    exporters:
      logging:
    service:
      pipelines:
        traces:
          receivers: [jaeger]
          processors: []
          exporters: [logging]
```

### StatefulSet

```yaml
apiVersion: opentelemetry.io/v1alpha1
kind: OpenTelemetryCollector
metadata:
  name: stateful
spec:
  mode: statefulset
  config: |
    receivers:
      jaeger:
        protocols:
          grpc:
    processors:
    exporters:
      logging:
    service:
      pipelines:
        traces:
          receivers: [jaeger]
          processors: []
          exporters: [logging]
```
