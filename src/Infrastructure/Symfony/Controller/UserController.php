<?php

namespace Infrastructure\Symfony\Controller;

use Domain\Entity\User;
use Domain\Request\AddNewUserRequest;
use Domain\Request\UpdateUserRequest;
use Domain\Service\User\UserServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class UserController extends AbstractController
{

    #[Route("/api/account", name: 'api.account.store', methods: ['POST'])]
    public function store(
        Request $request,
        UserServiceInterface $userService,
        SerializerInterface $serializer,
        AddNewUserRequest $newUserRequest
    ): Response {
        $deserializedUser = $serializer->deserialize($request->getContent(), User::class, 'json');

        $newUserRequest->setNewUser($deserializedUser);

        $response = $userService->addNewUser($newUserRequest);

        $serializedResponse = $serializer->serialize($response, 'json', [AbstractObjectNormalizer::SKIP_NULL_VALUES => true]);

        return new Response($serializedResponse, $response->statusCode, ['Content-Type' => "text/json"]);
    }

    #[Route("/api/account/{id}", name: 'api.account.view', methods: ['GET'])]
    public function view(
        int $id,
        SerializerInterface $serializer,
        UserServiceInterface $userService
    ) {

        $response = $userService->getUserById($id);

        $serializedResponse = $serializer->serialize($response, 'json', [AbstractObjectNormalizer::SKIP_NULL_VALUES => true]);
        return new Response($serializedResponse, $response->statusCode, ['Content-Type' => "text/json"]);
    }

    #[Route("/api/account/{id}", name: 'api.account.update', methods: ['PUT'])]
    public function update(
        int $id,
        Request $request,
        SerializerInterface $serializer,
        UpdateUserRequest $updateUserRequest,
        UserServiceInterface $userService
    ) {
        $data = json_decode($request->getContent(), true);

        $updateUserRequest->setData($id, $data);

        $response = $userService->updateUser($updateUserRequest);

        $serializedResponse = $serializer->serialize($response, 'json', [AbstractObjectNormalizer::SKIP_NULL_VALUES => true]);
        return new Response($serializedResponse, $response->statusCode, ['Content-Type' => "text/json"]);
    }

    #[Route('/api/simpleLoggedAction', name: 'api.simpleLoggedAction', methods: ['GET'])]
    public function loggedAction(): JsonResponse
    {
        return new JsonResponse(null, 200);
    }
}
