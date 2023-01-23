<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use Symfony\Component\Validator\Constraints as Assert;


#[ApiResource(
    operations: [
        new Post(uriTemplate:"/users/confirm"),

    ]
)]
class UserConfirmation
{
    #[Assert\Length(min: 30, max: 30)]
    #[Assert\NotBlank()]
    public  $confirmationToken;
}
