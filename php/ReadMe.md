# PHP

Reference Link: <https://opentelemetry.io/docs/instrumentation/php/getting-started/>

## Prerequisites

- PHP Version must be higher than 8.0+
- Composer: https://getcomposer.org/download/

- Installing dependencies
  ```bash
    pecl install opentelemetry-beta
    composer config allow-plugins.php-http/discovery false
    composer require \
      open-telemetry/sdk \
      open-telemetry/opentelemetry-auto-slim
  ```
