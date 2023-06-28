# Types of Sampler in Tracing Proxy

OpsRamp Tracing Proxy support the following types of samplers:

## DeterministicSampler

This is the simplest sampling algorithm - it is a static sample rate, choosing traces randomly to either keep or send (at the appropriate rate). It is not influenced by the contents of the trace.

### Config

```yaml
Sampler: DeterministicSampler

# SampleRate is the rate at which to sample. It indicates a ratio, where one
# sample trace is kept for every n traces seen. For example, a SampleRate of 30
# will keep 1 out of every 30 traces. The choice on whether to keep any specific
# trace is random, so the rate is approximate.
SampleRate: 1
```

## DynamicSampler

This sampler collects the values of a number of fields from a trace and uses them to form a key. This key is handed to the standard dynamic sampler algorithm which generates a sample rate based on the frequency with which that key has appeared in the previous ClearFrequencySec seconds.4

### Config

```yaml
Sampler: DynamicSampler

# SampleRate is the goal rate at which to sample. It indicates a ratio, where
# one sample trace is kept for every n traces seen. For example, a SampleRate of
# 30 will keep 1 out of every 30 traces. This rate is handed to the dynamic
# sampler, who assigns a sample rate for each trace based on the fields selected
# from that trace.
SampleRate: 2

# ClearFrequencySec is the name of the field the sampler will use to determine
# the period over which it will calculate the sample rate. This setting defaults
# to 30.
ClearFrequencySec: 60

# FieldList is a list of all the field names to use to form the key that will be handed to the dynamic sampler.
# The combination of values from all of these fields should reflect how interesting the trace is compared to
# another. A good field selection has consistent values for high-frequency, boring traffic, and unique values for
# outliers and interesting traffic. Including an error field (or something like HTTP status code) is an excellent
# choice. Using fields with very high cardinality (like `k8s.pod.id`), is a bad choice. If the combination of
# fields essentially makes them unique, the dynamic sampler will sample everything. If the combination of fields is
# not unique enough, you will not be guaranteed samples of the most interesting traces. As an example, consider a
# combination of HTTP endpoint (high-frequency and boring), HTTP method, and status code (normally boring but can
# become interesting when indicating an error) as a good set of fields since it will allowing proper sampling
# of all endpoints under normal traffic and call out when there is failing traffic to any endpoint.
# For example, in contrast, consider a combination of HTTP endpoint, status code, and pod id as a bad set of
# fields, since it would result in keys that are all unique, and therefore results in sampling 100% of traces.
# Using only the HTTP endpoint field would be a **bad** choice, as it is not unique enough and therefore
# interesting traces, like traces that experienced a `500`, might not be sampled.
# Field names may come from any span in the trace.
FieldList:
- request.method
- http.target
- response.status_code

# UseTraceLength will add the number of spans in the trace in to the dynamic
# sampler as part of the key. The number of spans is exact, so if there are
# normally small variations in trace length you may want to leave this off. If
# traces are consistent lengths and changes in trace length is a useful
# indicator of traces you'd like to see in OpsRamp, set this to true.
UseTraceLength: true

# AddSampleRateKeyToTrace when this is set to true, the sampler will add a field
# to the root span of the trace containing the key used by the sampler to decide
# the sample rate. This can be helpful in understanding why the sampler is
# making certain decisions about sample rate and help you understand how to
# better choose the sample rate key (aka the FieldList setting above) to use.
AddSampleRateKeyToTrace: true

# AddSampleRateKeyToTraceField is the name of the field the sampler will use
# when adding the sample rate key to the trace. This setting is only used when
# AddSampleRateKeyToTrace is true.
AddSampleRateKeyToTraceField: meta.tracing-proxy.dynsampler_key

```

## EMADynamicSampler (Exponential Moving Average (EMA) Dynamic Sampler)

Similar to simple DynamicSampler, it attempts to average a given sample rate, weighting rare traffic and frequent
traffic differently so as to end up with the correct average.

EMADynamicSampler is an improvement upon the simple DynamicSampler and is recommended
for most use cases. Based on the DynamicSampler implementation, EMADynamicSampler differs
in that rather than compute rate based on a periodic sample of traffic, it maintains an Exponential
Moving Average of counts seen per key, and adjusts this average at regular intervals.
The weight applied to more recent intervals is defined by `weight`, a number between
(0, 1) - larger values weight the average more toward recent observations. In other words,
a larger weight will cause sample rates more quickly adapt to traffic patterns,
while a smaller weight will result in sample rates that are less sensitive to bursts or drops
in traffic and thus more consistent over time.

Keys that are not found in the EMA will always have a sample
rate of 1. Keys that occur more frequently will be sampled on a logarithmic
curve. In other words, every key will be represented at least once in any
given window and more frequent keys will have their sample rate
increased proportionally to wind up with the goal sample rate.

### Config

```yaml
Sampler: EMADynamicSampler

# GoalSampleRate is the goal rate at which to sample. It indicates a ratio, where
# one sample trace is kept for every n traces seen. For example, a SampleRate of
# 30 will keep 1 out of every 30 traces. This rate is handed to the dynamic
# sampler, who assigns a sample rate for each trace based on the fields selected
# from that trace.
GoalSampleRate: 2

# AdjustmentInterval defines how often (in seconds) we adjust the moving average from
# recent observations.
AdjustmentInterval: 15

# Weight is a value between (0, 1) indicating the weighting factor used to adjust
# the EMA. With larger values, newer data will influence the average more, and older
# values will be factored out more quickly.  In mathematical literature concerning EMA,
# this is referred to as the `alpha` constant.
Weight: 0.5

# AgeOutValue indicates the threshold for removing keys from the EMA. The EMA of any key
# will approach 0 if it is not repeatedly observed, but will never truly reach it, so we have to
# decide what constitutes "zero". Keys with averages below this threshold will be removed
# from the EMA. Default is the same as Weight, as this prevents a key with the smallest
# integer value (1) from being aged out immediately. This value should generally be <= Weight,
# unless you have very specific reasons to set it higher.
AgeOutValue: 0.5

# BurstMultiple, if set, is multiplied by the sum of the running average of counts to define
# the burst detection threshold. If total counts observed for a given interval exceed the threshold
# EMA is updated immediately, rather than waiting on the AdjustmentInterval.
# Defaults to 2; negative value disables. With a default of 2, if your traffic suddenly doubles,
# burst detection will kick in.
BurstMultiple: 2

# BurstDetectionDelay indicates the number of intervals to run after Start is called before
# burst detection kicks in.
BurstDetectionDelay: 3

# MaxKeys, if greater than 0, limits the number of distinct keys tracked in EMA.
# Once MaxKeys is reached, new keys will not be included in the sample rate map, but
# existing keys will continue to be be counted. You can use this to keep the sample rate
# map size under control.
MaxKeys: 0

# FieldList is a list of all the field names to use to form the key that will be handed to the dynamic sampler.
# The combination of values from all of these fields should reflect how interesting the trace is compared to
# another. A good field selection has consistent values for high-frequency, boring traffic, and unique values for
# outliers and interesting traffic. Including an error field (or something like HTTP status code) is an excellent
# choice. Using fields with very high cardinality (like `k8s.pod.id`), is a bad choice. If the combination of
# fields essentially makes them unique, the dynamic sampler will sample everything. If the combination of fields is
# not unique enough, you will not be guaranteed samples of the most interesting traces. As an example, consider a
# combination of HTTP endpoint (high-frequency and boring), HTTP method, and status code (normally boring but can
# become interesting when indicating an error) as a good set of fields since it will allowing proper sampling
# of all endpoints under normal traffic and call out when there is failing traffic to any endpoint.
# For example, in contrast, consider a combination of HTTP endpoint, status code, and pod id as a bad set of
# fields, since it would result in keys that are all unique, and therefore results in sampling 100% of traces.
# Using only the HTTP endpoint field would be a **bad** choice, as it is not unique enough and therefore
# interesting traces, like traces that experienced a `500`, might not be sampled.
# Field names may come from any span in the trace.
FieldList:
- request.method
- http.target
- response.status_code

# UseTraceLength will add the number of spans in the trace in to the dynamic
# sampler as part of the key. The number of spans is exact, so if there are
# normally small variations in trace length you may want to leave this off. If
# traces are consistent lengths and changes in trace length is a useful
# indicator of traces you'd like to see in OpsRamp, set this to true.
UseTraceLength: true

# AddSampleRateKeyToTrace when this is set to true, the sampler will add a field
# to the root span of the trace containing the key used by the sampler to decide
# the sample rate. This can be helpful in understanding why the sampler is
# making certain decisions about sample rate and help you understand how to
# better choose the sample rate key (aka the FieldList setting above) to use.
AddSampleRateKeyToTrace: true

# AddSampleRateKeyToTraceField is the name of the field the sampler will use
# when adding the sample rate key to the trace. This setting is only used when
# AddSampleRateKeyToTrace is true.
AddSampleRateKeyToTraceField: meta.tracing-proxy.dynsampler_key
```

## RulesBasedSampler

This Sampler allows defining sampling rates based on the contents of the traces.
We allow defining of filters based on conditions on fields across all spans in a trace. For instance, if your root span has a status_code field, and the span wrapping your database call has an error field, you can define a condition that must be met on both fields, even though the two fields are technically separate events. You can supply a sample rate to use when a match is found, or optionally drop all events in that category.

Below specified config has the following examples:

Drop all traces for your load balancer's health-check endpoint
Keep all traces where the status code was 50x (sample rate of 1)
Dynamically sample 200 responses
Dynamically sample 200 string responses
Sample traces originating from a service

### Config

```yaml
Sampler: RulesBasedSampler
CheckNestedFields: false
rule:
- name: drop healthchecks
    drop: true
    condition:
    - field: http.route
        operator: '='
        value: /health-check
- name: keep slow 500 errors
    SampleRate: 1
    condition:
    - field: status_code
        operator: '='
        value: 500
    - field: duration_ms
        operator: '>='
        value: 1000.789
- name: dynamically sample 200 responses
    condition:
    - field: status_code
        operator: '='
        value: 200
    sampler:
    EMADynamicSampler:
        Sampler: EMADynamicSampler
        GoalSampleRate: 15
        FieldList:
        - request.method
        - request.route
        AddSampleRateKeyToTrace: true
        AddSampleRateKeyToTraceField: meta.tracing-proxy.dynsampler_key
- name: dynamically sample 200 string responses
    condition:
    - field: status_code
        operator: '='
        value: '200'
        datatype: int
    sampler:
    EMADynamicSampler:
        Sampler: EMADynamicSampler
        GoalSampleRate: 15
        FieldList:
        - request.method
        - request.route
        AddSampleRateKeyToTrace: true
        AddSampleRateKeyToTraceField: meta.tracing-proxy.dynsampler_key
- name: sample traces originating from a service
    Scope: span
    SampleRate: 5
    condition:
    - field: service name
        operator: '='
        value: users
    - field: meta.span_type
        operator: '='
        value: root
- SampleRate: 10
```

> **_NOTE:_** Rules are evaluated in order, and the first rule that matches is used. For this reason, define more specific rules at the top of the list of rules, and broader rules at the bottom. The conditions making up a rule are combined and must all evaluate to true for the rule to match. If no rules match, a configurable default sampling rate is applied.

Each rule has an optional name field, a SampleRate or sampler, and may include one or more condition. Use SampleRate to apply a static sample rate to traces that qualify for the given rule. Use a secondary sampler to apply a dynamic sample rate to traces that qualify for the given rule.

The sampling rate is determined in the following order:

1. Use a secondary sampler, if defined
2. Use the SampleRate field, which must not be less than 1
3. If drop = true is specified, then the trace will be omitted
4. A default sample rate of 1

Each condition in a rule consists of the following:

* the field within your spans that you would like to sample on
* the value which you are comparing the field to
* the operator which you are using to compare the field to the value
* an optional datatype parameter that coerces the field to match a specified type

The datatype parameter is optional and must be one of the following:

* "int"
* "float"
* "string"
* "bool"

The datatype parameter is helpful to let a rule handle multiple fields that come in as different data types. For example, it can be common that an http.status_code field comes in as either a string or an integer from different systems. Instead of writing the same rule twice, you can write it once and use the datatype parameter to coerce the field to the same type.

Condition operators:

* exists - does the field exist
* not-exists - does the field not exist
* != - is the value of the field not equal to the value in the rule
* = - is the value of the field equivalent to the value in the rule
* \> - is the value of the field greater than the value in the rule
* \>= - is the value of the field greater than or equal to the value in the rule
* < - is the value of the field less than the value in the rule
* \>= - is the value of the field less than or equal to the value in the rule
* starts-with - returns true if the field starts with the string defined in the value
* contains - returns true if the field contains the string defined in the value
* does-not-contain - returns true if the field does not contain the string defined in the value

> **_NOTE:_** Different data type values cant be compared and when comparison happens between int and float then both values are considered as float

### Using a Secondary Sampler

A secondary sampler can be specified using the sampler option. You can leverage any DynamicSampler, EMADynamicSampler, or TotalThroughputSampler to have precision of rule based sampler along with other forms of sampling

## TotalThroughputSampler

This sampler tries to hit a specified total through put per second in every node of the tracing proxy. It is most useful if you need to quickly get event volume under control, or if your traces are fairly uniform and a consistent volume of events is preferred. It performs poorly when the active keyspace is very large, so ideally the number of active keys should be be less than 10*GoalThroughputPerSec.

Sample rates are still calculated and set on the spans, but they are a function of the number of events seen for a key in a given window, as defined by ClearFrequencySec.

### Config

```yaml
Sampler: TotalThroughputSampler

# The goal rate of spans per second for a tracing proxy instance. 
# This rate is handed to the dynamic sampler which is then used to calculate a sample rate by 
# dividing counted events for that key by the desired number of events. 
# Defaults to 100, must be greater than 0. 
GoalThroughputPerSec: 100

# A list of the field names to use to form the key that will be handed to the dynamic sampler. 
# The cardinality of the combination of values from all of these keys should be reasonable in the face of the frequency of those keys. 
# Using too many fields to form your key can cause the sampler to struggle to meet your goal throughput rate
FieldList: '[request.method]'

# How often the rate counters are reset in seconds.
ClearFrequencySec: 30

# UseTraceLength will add the number of spans in the trace in to the dynamic
# sampler as part of the key. The number of spans is exact, so if there are
# normally small variations in trace length you may want to leave this off. If
# traces are consistent lengths and changes in trace length is a useful
# indicator of traces you'd like to see in OpsRamp, set this to true.
UseTraceLength: true

# AddSampleRateKeyToTrace when this is set to true, the sampler will add a field
# to the root span of the trace containing the key used by the sampler to decide
# the sample rate. This can be helpful in understanding why the sampler is
# making certain decisions about sample rate and help you understand how to
# better choose the sample rate key (aka the FieldList setting above) to use.
AddSampleRateKeyToTrace: true

# AddSampleRateKeyToTraceField is the name of the field the sampler will use
# when adding the sample rate key to the trace. This setting is only used when
# AddSampleRateKeyToTrace is true.
AddSampleRateKeyToTraceField: meta.tracing-proxy.dynsampler_key
```
