# b-sms notifications channel for Laravel 5.3+

This package makes it easy to send notifications using b-sms with Laravel 5.3+.


## Installation

You can install the package via composer:

```bash
composer require yna/laravel-b-sms
```

Then you must install the service provider:
```php
// config/app.php
'providers' => [
    ...
    Yna\BSms\BSmsServiceProvider::class,
],
```

### Setting up the PortToSms service

Add your PortToSms account, user, password and default sender name (or phone number) to your `config/services.php`:

```php
// config/services.php
...
'bsms' => [
    'user' => env('BSMS_USER'),
    'password' => env('BSMS_PASSWORD'),
    'sender' => env('BSMS_SENDER')
],
...
```

## Usage

You can use the channel in your `via()` method inside the notification:

```php
use Illuminate\Notifications\Notification;
use Yna\PortToSms\PortToSmsMessage;
use Yna\PortToSms\PortToSmsChannel;

class AccountApproved extends Notification
{
    public function via($notifiable)
    {
        return [BSmsChannel::class];
    }

    public function toBSms($notifiable)
    {
        return BSmsMessage::create("Task #{$notifiable->id} is complete!");
    }
}
```

In your notifiable model, make sure to include a routeNotificationForPort2sms() method, which return the phone number.

```php
public function routeNotificationForBSms()
{
    return $this->phone;
}
```

### Available methods

`from()`: Sets the sender's name or phone number.

`content()`: Set a content of the notification message.

`sendAt()`: Set a time for scheduling the notification message.

## Security

If you discover any security related issues, please email security@yna.co.il instead of using the issue tracker.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
