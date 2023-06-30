<?php

namespace App\Service;

use App\Entity\FormHistory;
use App\Repository\FormHistoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Validation;

class FormHistoryService
{
    private $validator;
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly FormHistoryRepository $formHistoryRepository
    ) {
        $encoders = [new JsonEncoder()];
        $normalizers = [new ArrayDenormalizer(), new ObjectNormalizer()];
        $this->serializer = new Serializer($normalizers, $encoders);
        $this->validator = Validation::createValidator();
    }
    private function validateDefault(string $text){

        $errors = $this->validator->validate($text, [
            new NotBlank(),
        ]);

        if (count($errors) > 0) {
            return false;
        }
        return true;
    }
    private function validateEmail(string $email){
        $errors = $this->validator->validate($email, [
            new NotBlank(),
            new Email()
        ]);

        if (count($errors) > 0) {
            return false;
        }
        return true;
    }

    public function validateUserForm(string $firstName,string $lastName, string $email){
        if(
            $this->validateDefault($firstName)
            && $this->validateDefault($lastName)
            && $this->validateEmail($email)
        ){
            $formHistory = new FormHistory();
            $formHistory->setFirstName($firstName);
            $formHistory->setLastName($lastName);
            $formHistory->setEmail($email);
            $this->entityManager->persist($formHistory);
            $this->entityManager->flush();
            return true;
        }else {
            return false;
        }
    }
    public function getAllUsers(){
        $users = $this->formHistoryRepository->findAll();
        $json = $this->serializer->serialize($users, 'json');
        return $json;
    }

}