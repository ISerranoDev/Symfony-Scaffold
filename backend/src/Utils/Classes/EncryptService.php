<?php

namespace App\Utils\Classes;

use ParagonIE\Halite\Alerts\CannotPerformOperation;
use ParagonIE\Halite\Alerts\InvalidDigestLength;
use ParagonIE\Halite\Alerts\InvalidKey;
use ParagonIE\Halite\Alerts\InvalidMessage;
use ParagonIE\Halite\Alerts\InvalidSignature;
use ParagonIE\Halite\Alerts\InvalidType;
use ParagonIE\Halite\HiddenString;
use ParagonIE\Halite\KeyFactory;
use ParagonIE\Halite\Symmetric\Crypto;
use Psr\Container\ContainerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\Service\Attribute\Required;

class EncryptService
{


    const KEY_FILE_PATH = 'encryption/encryption.key';
    const KEY_FOLDER_PATH = 'encryption';
    private ParameterBagInterface $parameterBag;

    #[Required]
    public function configure(ParameterBagInterface $parameterBag): void
    {
        $this->parameterBag = $parameterBag;
    }

    /**
     * @param string $data
     * @return string
     * @throws CannotPerformOperation
     * @throws InvalidDigestLength
     * @throws InvalidKey
     * @throws InvalidMessage
     * @throws InvalidType
     * @throws \SodiumException
     */
    public function encryptData(string $data): string
    {

        $key = KeyFactory::loadEncryptionKey('./../' . self::KEY_FILE_PATH);

        return Crypto::encrypt(
            new HiddenString($data),
            $key
        );
    }

    /**
     * @param string $encryptedData
     * @return string
     * @throws CannotPerformOperation
     * @throws InvalidDigestLength
     * @throws InvalidKey
     * @throws InvalidMessage
     * @throws InvalidSignature
     * @throws InvalidType
     * @throws \SodiumException
     */
    public function decryptData(string $encryptedData): string
    {

        $key = KeyFactory::loadEncryptionKey('./../' . self::KEY_FILE_PATH);

        return Crypto::decrypt(
            $encryptedData,
            $key
        )->getString();

    }

    /**
     * @param string $data
     * @return string
     */
    public function hashData(string $data): string
    {
        // Create a cipher of the appropriate length for this method.
        $iv = '0000000000000000';

        // Create the encryption.
        $ciphertext = openssl_encrypt(
            $data,
            $this->getHashMethod(),
            $this->getHashKey(),
            OPENSSL_RAW_DATA,
            $iv
        );

        // Prefix the encoded text with the iv and encode it to base 64. Append the encoded suffix.
        return base64_encode($iv.$ciphertext);
    }

    /**
     * @param string $encodedData
     * @return string
     */
    public function unHashData(string $encodedData): string
    {
        // If the value is an object or null then ignore

        // If the data was just <ENC> the return null;
        if (empty($encodedData)) {
            return $encodedData;
        }

        $data = base64_decode($encodedData);

        $ivsize = openssl_cipher_iv_length($this->getHashMethod());
        $iv = mb_substr($data, 0, $ivsize, '8bit');
        $ciphertext = mb_substr($data, $ivsize, null, '8bit');

        return openssl_decrypt(
            $ciphertext,
            $this->getHashMethod(),
            $this->getHashKey(),
            OPENSSL_RAW_DATA,
            $iv
        );
    }

    private function getHashMethod(): string
    {
        return $this->parameterBag->get('hash-method');
    }

    private function getHashKey(): string
    {
        return $this->parameterBag->get('hash-key');
    }

}
