# TP_cloudAMQP
Parfait 👍 on fait ça **en PHP**, simple et propre.

On va utiliser la librairie **php-amqplib** (la plus utilisée pour RabbitMQ en PHP).

---

## 🧩 Prérequis

### 1️⃣ Installer la librairie PHP RabbitMQ

Avec **Composer** (recommandé) :

```bash
composer require php-amqplib/php-amqplib
```

---

## 🔑 Infos CloudAMQP

Tu dois avoir une URL du type :

```
amqps://user:password@host/vhost
```

👉 On va la découper automatiquement en PHP.

---

## 1️⃣ Programme 1 — envoyer un message (producer.php)

```php
<?php
require_once __DIR__ . '/vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

// URL CloudAMQP
$url = "amqps://tqaoitos:JUsqKoAdXa_lqeQ2Q59I3FfueYa3UpyX@dog.lmq.cloudamqp.com/tqaoitos";
$parts = parse_url($url);

$connection = new AMQPStreamConnection(
    $parts['host'],
    5672,
    $parts['user'],
    $parts['pass'],
    ltrim($parts['path'], '/'),
    false,
    'AMQPLAIN',
    null,
    'en_US',
    3.0,
    3.0,
    null,
    false,
    5671 // port SSL
);

$channel = $connection->channel();

// Déclarer la file
$channel->queue_declare('ma_file', false, true, false, false);

$messageBody = "Bonjour depuis le programme PHP 1 👋";
$msg = new AMQPMessage($messageBody);

$channel->basic_publish($msg, '', 'ma_file');

echo "Message envoyé : $messageBody\n";

$channel->close();
$connection->close();
```

---

## 2️⃣ Programme 2 — lire et afficher le message (consumer.php)

```php
<?php
require_once __DIR__ . '/vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;

// URL CloudAMQP
$url = "amqps://tqaoitos:JUsqKoAdXa_lqeQ2Q59I3FfueYa3UpyX@dog.lmq.cloudamqp.com/tqaoitos";
$parts = parse_url($url);

$connection = new AMQPStreamConnection(
    $parts['host'],
    5672,
    $parts['user'],
    $parts['pass'],
    ltrim($parts['path'], '/'),
    false,
    'AMQPLAIN',
    null,
    'en_US',
    3.0,
    3.0,
    null,
    false,
    5671
);

$channel = $connection->channel();

$channel->queue_declare('ma_file', false, true, false, false);

echo "En attente de messages...\n";

$callback = function ($msg) {
    echo "Message reçu : " . $msg->body . "\n";
};

$channel->basic_consume('ma_file', '', false, true, false, false, $callback);

while ($channel->is_consuming()) {
    $channel->wait();
}

$channel->close();
$connection->close();
```

---

## 🧪 Comment tester

1. Ouvre 2 terminaux
2. Dans le premier :

   ```bash
   php programme-publish.php
   ```
3. Dans le deuxième :

   ```bash
   php programme-listener.php
   ```

➡️ Tu verras le message s’afficher côté consumer 🎉

---

## ⚠️ Points importants à connaître

* `queue_declare` doit être **identique** dans les deux programmes
* `amqps` = connexion sécurisée (CloudAMQP l’exige)
* Tu peux envoyer :

  * du texte
  * du JSON
  * des objets sérialisés

---

## ➕ Améliorations possibles

Si tu veux aller plus loin, je peux te montrer :

* envoi de **JSON**
* `ack` manuel (meilleure fiabilité)
* **plusieurs consumers**
* gestion des erreurs
* version **Docker**
* différence **exchange / queue / routing key**


