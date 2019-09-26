---
apiVersion: serving.knative.dev/v1alpha1
kind: Service
metadata:
  name: {{ $service }}-artisan
  namespace: {{ $namespace }}
  labels:
    serving.knative.dev/visibility: 'cluster-local'
spec:
  template:
    metadata:
      annotations:
@if ($artisan['useHPA'])

        autoscaling.knative.dev/maxScale: "2"
        autoscaling.knative.dev/metric: cpu
        autoscaling.knative.dev/class: hpa.autoscaling.knative.dev
@endif
    spec:
      containers:
        - image: {{ $image }}
          resources:
            requests:
              memory: {{ $artisan['requests']['memory'] }}
              cpu: {{ $artisan['requests']['cpu'] }}
            limits:
              memory: {{ $artisan['limits']['memory'] }}
              cpu: {{ $artisan['limits']['cpu'] }}
          env:
            - name: HANDLER
              value: artisan
            @foreach($environment as $name => $value)

            - name: {{ $name }}
              value: '{{ $value }}'
            @endforeach

---
apiVersion: serving.knative.dev/v1alpha1
kind: Service
metadata:
  name: {{ $service }}-website
  namespace: {{ $namespace }}
spec:
  template:
    metadata:
      annotations:
@if ($website['useHPA'])

        autoscaling.knative.dev/maxScale: "4"
        autoscaling.knative.dev/metric: cpu
        autoscaling.knative.dev/class: hpa.autoscaling.knative.dev
@endif
    spec:
      containers:
        - image: {{ $image }}
          resources:
            requests:
              memory: {{ $website['requests']['memory'] }}
              cpu: {{ $website['requests']['cpu'] }}
            limits:
              memory: {{ $website['limits']['memory'] }}
              cpu: {{ $website['limits']['cpu'] }}
          env:
            - name: HANDLER
              value: website
            @foreach($environment as $name => $value)

            - name: {{ $name }}
              value: '{{ $value }}'
            @endforeach

---
apiVersion: sources.eventing.knative.dev/v1alpha1
kind: CronJobSource
metadata:
  name: {{ $service }}-artisan-schedule
  namespace: {{ $namespace }}
spec:
  schedule: "* * * * *"
  data: |
    ["schedule:run", "--no-ansi", "--no-interaction"]
  sink:
    apiVersion: serving.knative.dev/v1alpha1
    kind: Service
    name: {{ $service }}-artisan

@if ($queue['enabled'] && $queue['url'])
---
apiVersion: sources.eventing.knative.dev/v1alpha1
kind: AwsSqsSource
metadata:
  name: {{ $service }}-artisan-aws-queue
  namespace: {{ $namespace }}
spec:
  queueUrl: {{ $queue['url'] }}
  awsCredsSecret:
    name: {{ $service }}-artisan-aws-queue-secret
    key: credentials
  sink:
    apiVersion: serving.knative.dev/v1alpha1
    kind: Service
    name: {{ $service }}-artisan
@endif
