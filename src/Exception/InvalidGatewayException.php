<?php

// PaymentProcessingException.php
namespace App\Exception;

class PaymentProcessingException extends \RuntimeException {}

// InvalidGatewayException.php
namespace App\Exception;

class InvalidGatewayException extends \InvalidArgumentException {}