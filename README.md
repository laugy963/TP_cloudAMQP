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


Oui 🙂 **obligatoirement**.

### 🔐 Pourquoi un mot de passe est nécessaire ?

CloudAMQP est un **service distant** et **sécurisé**.
Pour s’y connecter, RabbitMQ exige toujours :

* un **nom d’utilisateur**
* un **mot de passe**

👉 Ces infos sont **fournies automatiquement par CloudAMQP**.

---

## 📌 Où est le mot de passe ?

Dans l’URL CloudAMQP que tu as reçue :

```
amqps://USER:PASSWORD@HOST/VHOST
```

Exemple :

```
amqps://abcd1234:xyz987@cow.rmq.cloudamqp.com/abcd1234
```

* **USER** → `abcd1234`
* **PASSWORD** → `xyz987`
* **HOST** → `cow.rmq.cloudamqp.com`
* **VHOST** → `abcd1234`

👉 Tu **n’inventes rien**, tu recopies ce que CloudAMQP te donne.

---

## 🧠 Bonne pratique (important)

❌ **Ne mets jamais le mot de passe en dur** dans le code en production.

### ✅ Utilise une variable d’environnement

```bash
export CLOUDAMQP_URL="amqps://user:password@host/vhost"
```

Puis en PHP :

```php
$url = getenv('CLOUDAMQP_URL');
```

---

## 🧪 En local (pour apprendre)

Pour tes tests, tu peux le mettre en dur sans souci 👍

---

## 🟢 Récap

* Oui, **mot de passe obligatoire**
* Il est **dans l’URL CloudAMQP**
* CloudAMQP gère l’authentification
* Le programme 1 et 2 utilisent **la même URL**

Si tu veux, je peux aussi t’expliquer :

* comment **voir les messages** depuis l’interface CloudAMQP
* comment **changer les identifiants**
* comment faire **sans CloudAMQP (RabbitMQ local)**

Dis-moi 👌
