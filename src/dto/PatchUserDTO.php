<?php

namespace App\DTO;

class PatchUserDTO {
    public ?string $name = null;
    public ?string $email = null;
    public ?string $phone = null;
    public ?string $address = null;
    public ?string $password = null;
    public ?string $cc = null;

    public function __construct(array $data) {
        $this->name = $data['name'] ?? null;
        $this->email = $data['email'] ?? null;
        $this->phone = $data['phone'] ?? null;
        $this->address = $data['address'] ?? null;
        $this->password = $data['password'] ?? null;
        $this->cc = $data['cc'] ?? null;
    }

    public function toArray(): array {
        $data = [];

        if ($this->name !== null) $data['name'] = $this->name;
        if ($this->email !== null) $data['email'] = $this->email;
        if ($this->phone !== null) $data['phone'] = $this->phone;
        if ($this->address !== null) $data['address'] = $this->address;
        if ($this->password !== null) $data['password'] = $this->password;
        if ($this->cc !== null) $data['cc'] = $this->cc;

        return $data;
    }
}
