<?php

namespace App\DTO;

class CreateUserDTO {
    public string $name;
    public string $email;
    public string $phone;
    public string $address;
    public string $password;
    public string $cc;

    public function __construct(array $data) {
        if (!isset($data['name'], $data['email'], $data['phone'], $data['address'], $data['password'], $data['cc'])) {
            throw new \InvalidArgumentException('Faltan datos requeridos para el usuario');
        }

        $this->name = $data['name'];
        $this->email = $data['email'];
        $this->phone = $data['phone'];
        $this->address = $data['address'];
        $this->password = $data['password'];
        $this->cc = $data['cc'];
    }

    public function toArray(): array {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'password' => $this->password,
            'cc' => $this->cc,
        ];
    }
}
