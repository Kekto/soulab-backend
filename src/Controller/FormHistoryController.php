<?php

namespace App\Controller;

use App\Service\FormHistoryService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FormHistoryController extends AbstractController
{
    public function __construct(
        private readonly FormHistoryService $formHistoryService
    ) {
    }

    #[Route('/user/history', name: 'app_form_history_post',methods: 'POST')]
    public function saveFormHistory(Request $request): Response
    {
//        $firstName = $request->get('firstName');
//        $lastName = $request->get('lastName');
//        $email = $request->get('email');
        $parameters = json_decode($request->getContent(), true);

        if(!$this->formHistoryService->validateUserForm(
            $parameters['firstName'],
            $parameters['lastName'],
            $parameters['email'])
        ){
            return new JsonResponse('Something has gone wrong with the validation',400);
        }else{
            return new JsonResponse('Validation completed succesfuly',200);
        }
    }
    #[Route('/user/history', name: 'app_form_history_get',methods: 'GET')]
    public function getAllUsers(){
        return new Response($this->formHistoryService->getAllUsers(),200);
    }
}
