<?php
//
//namespace App\Serializer;
//
//use App\Entity\User;
////use Symfony\Bundle\SecurityBundle\Security;
//use Symfony\Bundle\SecurityBundle\Security;
//use Symfony\Component\Security\Core\User\UserInterface;
//use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
//use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
//use Symfony\Component\Serializer\SerializerAwareInterface;
//use Symfony\Component\Serializer\SerializerAwareTrait;
//use Symfony\Component\Security\Csrf\TokenStorage\TokenStorageInterface;
//
//class UserAttributeNormalizer implements ContextAwareNormalizerInterface, SerializerAwareInterface
//{
//    use SerializerAwareTrait;
//
//    const USER_ATTRIBUTE_NORMALIZER_ALREADY_CALLED = 'USER_ATTRIBUTE_NORMALIZER_ALREADY_CALLED';
//
//    private $tokenStorage;
//    public function __construct(TokenStorageInterface $tokenStorage){
//        $this->$tokenStorage = $tokenStorage;
//    }
//
//    public function supportsNormalization($data, $format = null, array $context = []): bool
//    {
//        if (isset($context[self::USER_ATTRIBUTE_NORMALIZER_ALREADY_CALLED])) {
//            return false;
//        }
//
//        return $data instanceof User;
//    }
//
//    public function normalize($object, $format = null, array $context = [])
//    {
//        if ($this->isUserHimself($object)) {
//            $context['groups'][] = 'get-owner';
//        }
//
//        // Now continue with serialization
//        return $this->passOn($object, $format, $context);
//    }
//
//    private function isUserHimself($object)
//    {
//        /**
//         * @var UserInterface $author
//         */
//        $author = $this->$tokenStorage->getUser();
//        return $object->getUsername() === $author;
//    }
//
//    private function passOn($object, $format, $context)
//    {
//        if (!$this->serializer instanceof NormalizerInterface) {
//            throw new \LogicException(
//                sprintf('Cannot normalize object "%s" becouse the injected serializer is not a normalizer.', $object)
//            );
//        }
//
//        $context[self::USER_ATTRIBUTE_NORMALIZER_ALREADY_CALLED] = true;
//
//        return $this->serializer->normalize($object, $format, $context);
//    }
//}
