# vsHomeBridge
Simple PHP Method to connect to HomeBridge API
<br><br>
vsHomeBridge is a PHP class designed to facilitate communication with the Homebridge API. It simplifies the process of connecting to a Homebridge server, authenticating, and performing actions like retrieving information about connected accessories. This tool is particularly useful for developers and system integrators looking to integrate Homebridge-controlled devices into their PHP-based applications or services.
Features

Easy-to-use PHP interface to communicate with Homebridge.
Supports authentication with the Homebridge API.
Retrieve a list of all connected accessories.
Fetch detailed information about specific accessories, including their characteristics and states.

Requirements
```
    PHP 7.0 or higher.
    cURL enabled in PHP (usually enabled by default).
```

Installation

You can simply copy the vsHomebridge class file into your project and include it in your PHP script. Ensure that your PHP environment is set up with cURL support.

```
require_once 'path/to/vsHomeBridge.php';
```

Usage

Here’s a basic guide on how to use the vsHomeBridge class:
Initialize the Class

First, create an instance of the vsHomebridge class by passing the IP address of your Homebridge server.

```
$homebridge = new vsHomeBridge('your_homebridge_ip');
```

Login

Authenticate with the Homebridge API using the default or provided username and password.

```
$loginSuccess = $homebridge->login('your_username', 'your_password');
if (!$loginSuccess) {
    die("Failed to login.");
}
```

Get Accessories

Retrieve a list of all accessories registered with the Homebridge.

```
$accessories = $homebridge->getAccessories();
print_r($accessories);
```

Get Specific Accessory Information

Fetch detailed information about a specific accessory using its unique identifier.

```
$accessory = $homebridge->getAccessory('unique_id_of_accessory');
print_r($accessory);
```

Advanced Usage

The vsHomebridge class also provides a method to get a simplified list of accessory information suitable for certain applications:

```
$simplifiedAccessories = $homebridge->getSIMAccessories();
print_r($simplifiedAccessories);
```

Contributing

Contributions to vsHomeBridge are welcome! Feel free to fork the repository, make your changes, and submit a pull request.

