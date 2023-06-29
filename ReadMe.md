# Tracing Documentation

## Span

```json
{
    "name": "hello-greetings",
    "context": {
        "trace_id": "0x5b8aa5a2d2c872e8321cf37308d69df2",
        "span_id": "0x5fb397be34d26b51"
    },
    "parent_id": "0x051581bf3cb55c13",
    "start_time": "2022-04-29T18:52:58.114304Z",
    "end_time": "2022-04-29T22:52:58.114561Z",
    "attributes": {
        "http.route": "some_route1",
        "net.host.ip": "10.177.2.152",
        "net.host.port": "26040",
        "http.method": "GET",
        "http.target": "/v1/sys/health",
        "http.server_name": "mortar-gateway",
        "http.route": "/v1/sys/health",
        "http.user_agent": "Consul Health Check",
        "http.scheme": "http",
        "http.host": "10.177.2.152:26040",
        "http.flavor": "1.1"
    },
    "events": [
        {
            "name": "hey there!",
            "timestamp": "2022-04-29T18:52:58.114561Z",
            "attributes": {
                "event_attributes": 1
            }
        },
        {
            "name": "bye now!",
            "timestamp": "2022-04-29T18:52:58.114585Z",
            "attributes": {
                "event_attributes": 1
            }
        }
    ]
}
```

## Resource

```json
{
    "resource": {
        "attributes": {
            "host": "ub-22-007",
            "service.name": "http-sever",
            "service.namespace": "production",
            "service.instance.id": "627cc493-f310-47de-96bd-71410b7dec09",
            "service.version": "1.1.1"
        }
    }
}
```

## Complete Trace at high level

```json
{
    "resource": {
        "attributes": {
            "host": "ub-22-007",
            "service.name": "http-sever",
            "service.namespace": "production",
            "service.instance.id": "627cc493-f310-47de-96bd-71410b7dec09",
            "service.version": "1.1.1"
        }
    },
    "scope_spans": [
        {
            "name": "hello",
            "context": {
                "trace_id": "0x5b8aa5a2d2c872e8321cf37308d69df2",
                "span_id": "0x051581bf3cb55c13"
            },
            "parent_id": null,
            "start_time": "2022-04-29T18:52:58.114201Z",
            "end_time": "2022-04-29T18:52:58.114687Z",
            "attributes": {
                "http.route": "some_route3"
            },
            "events": [
                {
                    "name": "Guten Tag!",
                    "timestamp": "2022-04-29T18:52:58.114561Z",
                    "attributes": {
                        "event_attributes": 1
                    }
                }
            ]
        },
        {
            "name": "hello-greetings",
            "context": {
                "trace_id": "0x5b8aa5a2d2c872e8321cf37308d69df2",
                "span_id": "0x5fb397be34d26b51"
            },
            "parent_id": "0x051581bf3cb55c13",
            "start_time": "2022-04-29T18:52:58.114304Z",
            "end_time": "2022-04-29T22:52:58.114561Z",
            "attributes": {
                "http.route": "some_route1",
                "net.host.ip": "10.177.2.152",
                "net.host.port": "26040",
                "http.method": "GET",
                "http.target": "/v1/sys/health",
                "http.server_name": "mortar-gateway",
                "http.route": "/v1/sys/health",
                "http.user_agent": "Consul Health Check",
                "http.scheme": "http",
                "http.host": "10.177.2.152:26040",
                "http.flavor": "1.1"
            },
            "events": [
                {
                    "name": "hey there!",
                    "timestamp": "2022-04-29T18:52:58.114561Z",
                    "attributes": {
                        "event_attributes": 1
                    }
                },
                {
                    "name": "bye now!",
                    "timestamp": "2022-04-29T18:52:58.114585Z",
                    "attributes": {
                        "event_attributes": 1
                    }
                }
            ]
        }
    ]
}
```
