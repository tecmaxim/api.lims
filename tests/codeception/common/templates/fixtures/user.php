<?php
/**
 * @var $faker \Faker\Generator
 * @var $index integer
 */

$security = Yii::$app->getSecurity();

$objDateTime = new DateTime('NOW');

return [
    'username' => $faker->userName,
    'email' => $faker->email,
    'authKey' => $security->generateRandomString(),
    'passwordHash' => $security->generatePasswordHash('password_' . $index),
    'passwordResetToken' => $security->generateRandomString() . '_' . time(),
    'createdAt' => $objDateTime->format('c');,
    'updatedAt' => $objDateTime->format('c');,
];
