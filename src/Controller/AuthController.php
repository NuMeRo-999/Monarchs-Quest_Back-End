<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\User\UserInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends AbstractController
{
    private $jwtManager;
    private $userProvider;
    private $passwordEncoder;

    public function __construct(JWTTokenManagerInterface $jwtManager, UserProviderInterface $userProvider, UserPasswordHasherInterface $passwordEncoder)
    {
        $this->jwtManager = $jwtManager;
        $this->userProvider = $userProvider;
        $this->passwordEncoder = $passwordEncoder;
    }

    public function login(Request $request)
    {
        // Obtener las credenciales de la solicitud
        $credentials = json_decode($request->getContent(), true);

        if (!$credentials || !isset($credentials['username']) || !isset($credentials['password'])) {
            throw new BadCredentialsException('Credenciales inválidas.');
        }

        // Validar las credenciales
        $user = $this->userProvider->loadUserByIdentifier($credentials['username']);

        if (!$user) {
            throw new BadCredentialsException('Usuario no encontrado.');
        }

        // Verificar la contraseña
        if (!$this->passwordEncoder->isPasswordValid($user, $credentials['password'])) {
            throw new BadCredentialsException('Contraseña incorrecta.');
        }

        // Generar el token JWT
        $token = $this->jwtManager->create($user);

        // Devolver el token JWT en la respuesta
        return new JsonResponse(['token' => $token]);
    }

    #[Route('/register', name: 'user_register', methods: ['POST'])]
    public function register(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        // Obtiene los datos del cuerpo de la solicitud
        $data = json_decode($request->getContent(), true);

        // Verifica si se han proporcionado el correo electrónico y la contraseña
        if (!isset($data['username']) || !isset($data['password'])) {
            return new JsonResponse(['error' => 'Email and password are required'], Response::HTTP_BAD_REQUEST);
        }

        // Crea una nueva instancia de la entidad User
        $user = new User();
        $user->setUsername($data['username']);

        // Codifica la contraseña
        $encodedPassword = $passwordHasher->hashPassword($user, $data['password']);
        $user->setPassword($encodedPassword);

        // Guarda el usuario en la base de datos
        $entityManager->persist($user);
        $entityManager->flush();

        // Devuelve una respuesta de éxito
        return new JsonResponse(['message' => 'User registered successfully'], Response::HTTP_CREATED);
    }
}
